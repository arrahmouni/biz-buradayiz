<?php

namespace Modules\Platform\Http\Services;

use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Auth\Models\User;
use Modules\Base\Http\Services\BaseCrudService;
use Modules\Notification\Enums\NotificationChannels;
use Modules\Notification\Enums\NotificationPriority;
use Modules\Notification\Http\Services\NotificationService;
use Modules\Platform\Enums\BillingPeriod;
use Modules\Platform\Enums\PackageSubscriptionPaymentMethod;
use Modules\Platform\Enums\PackageSubscriptionPaymentStatus;
use Modules\Platform\Enums\PackageSubscriptionStatus;
use Modules\Platform\Enums\permissions\PackageSubscriptionPermissions;
use Modules\Platform\Models\Package;
use Modules\Platform\Models\PackageSubscription as CrudModel;
use Modules\Platform\Models\PackageSubscriptionSnapshot;
use Yajra\DataTables\Facades\DataTables;

class PackageSubscriptionService extends BaseCrudService
{
    protected $modelClass = CrudModel::class;

    protected $unnecessaryFieldsForCrud = [
        'package_id',
        'notify_user',
    ];

    protected $modelScopes = ['user', 'snapshot'];

    public function createModel(array $data): CrudModel
    {
        return DB::transaction(function () use ($data) {
            $package = Package::with('translations')->findOrFail($data['package_id']);
            $modelData = $this->prepareModelData($data);
            $modelData['remaining_connections'] = $package->connections_count;

            if ($package->is_free_tier) {
                $modelData['status'] = PackageSubscriptionStatus::Active->value;
                $modelData['payment_status'] = PackageSubscriptionPaymentStatus::Paid->value;
                $modelData['payment_method'] = PackageSubscriptionPaymentMethod::Other->value;
            }

            $this->applyPaidBusinessRules($modelData, null, $package);
            $this->applyCreateSubscriptionTimeline($modelData, $package);

            $subscription = CrudModel::query()->create($modelData);
            $subscription->snapshot()->create(PackageSubscriptionSnapshot::attributesFromPackage($package));

            return $subscription->load('snapshot');
        });
    }

    public function updateModel(CrudModel $model, array $data): CrudModel
    {
        return DB::transaction(function () use ($model, $data) {
            $notifyUser = (bool) ($data['notify_user'] ?? false);

            $model->refresh();
            $model->loadMissing(['user', 'snapshot']);

            if ($model->isFreeTierCatalogSubscription()) {
                throw new AuthorizationException(
                    trans('admin::validation.package_subscription.free_tier_cannot_be_modified')
                );
            }

            $modelData = [
                'status' => $data['status'],
                'payment_status' => $data['payment_status'],
            ];

            $newStatus = PackageSubscriptionStatus::from((string) $modelData['status']);
            if ($newStatus === PackageSubscriptionStatus::Cancelled) {
                $modelData['cancelled_at'] = $model->cancelled_at ?? now();
            } else {
                $modelData['cancelled_at'] = null;
            }

            $this->applyPaidBusinessRules($modelData, $model, null);
            $this->fillActivationTimelineFromSnapshotIfNeeded($model, $modelData);
            $this->carryOverFreeTierConnectionsOnFirstPaidActivation($model, $modelData);
            $this->clearSubscriptionTimelineUnlessActive($modelData);
            $model->update($modelData);

            $model = $model->fresh(['user', 'snapshot']);

            // if ($notifyUser && $model->user) {
            //     $this->notifySubscriberAboutSubscriptionUpdate($model->user, $model);
            // }

            return $model;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function applyPaidBusinessRules(array &$data, ?CrudModel $existing = null, ?Package $packageFromRequest = null): void
    {
        $newPayment = PackageSubscriptionPaymentStatus::from($data['payment_status']);

        if ($newPayment === PackageSubscriptionPaymentStatus::Paid) {
            $wasPaid = $existing && $existing->payment_status === PackageSubscriptionPaymentStatus::Paid;
            if (! $wasPaid) {
                $data['status'] = PackageSubscriptionStatus::Active->value;
            }
            if (! $wasPaid && empty($data['paid_at'])) {
                $data['paid_at'] = now();
            }
        }

        if ($newPayment === PackageSubscriptionPaymentStatus::Paid
            && $existing
            && $existing->remaining_connections === null) {
            $connectionsCount = $packageFromRequest?->connections_count
                ?? $existing->snapshot?->connections_count;
            if ($connectionsCount !== null) {
                $data['remaining_connections'] = $connectionsCount;
            }
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function applyCreateSubscriptionTimeline(array &$data, Package $package): void
    {
        $status = PackageSubscriptionStatus::tryFrom((string) ($data['status'] ?? ''));

        if ($status !== PackageSubscriptionStatus::Active) {
            $data['starts_at'] = null;
            $data['ends_at'] = null;
            $data['paid_at'] = null;

            return;
        }

        $startsAt = now();
        $data['starts_at'] = $startsAt;
        $data['ends_at'] = CrudModel::calculateEndsAtFromPackageBilling($package, $startsAt);
        $data['paid_at'] = $startsAt;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function clearSubscriptionTimelineUnlessActive(array &$data): void
    {
        $status = PackageSubscriptionStatus::tryFrom((string) ($data['status'] ?? ''));

        if ($status !== PackageSubscriptionStatus::Active) {
            $data['starts_at'] = null;
            $data['ends_at'] = null;
            $data['paid_at'] = null;
        }
    }

    /**
     * When a subscription becomes active and paid but has no period yet, derive it from the snapshot (same rules as catalog billing).
     *
     * @param  array<string, mixed>  $data
     */
    protected function fillActivationTimelineFromSnapshotIfNeeded(CrudModel $existing, array &$data): void
    {
        $status = PackageSubscriptionStatus::tryFrom((string) ($data['status'] ?? ''));
        $payment = PackageSubscriptionPaymentStatus::tryFrom((string) ($data['payment_status'] ?? ''));

        if ($status !== PackageSubscriptionStatus::Active || $payment !== PackageSubscriptionPaymentStatus::Paid) {
            return;
        }

        if (! empty($data['starts_at']) || $existing->starts_at) {
            return;
        }

        $snapshot = $existing->snapshot ?? $existing->snapshot()->first();
        if (! $snapshot) {
            return;
        }

        $billing = BillingPeriod::tryFrom((string) ($snapshot->billing_period ?? ''))
            ?? BillingPeriod::OneTime;

        $startsAt = Carbon::now();
        $data['starts_at'] = $startsAt;
        $data['ends_at'] = match ($billing) {
            BillingPeriod::Monthly => $startsAt->copy()->addMonth(),
            BillingPeriod::Yearly => $startsAt->copy()->addYear(),
            BillingPeriod::OneTime => null,
        };
        if (empty($data['paid_at'])) {
            $data['paid_at'] = $startsAt;
        }
    }

    /**
     * When a paid catalog subscription is verified for the first time, merge any remaining connection
     * quota from the provider's active free-tier subscription(s) into this subscription and retire the free tier rows.
     *
     * @param  array<string, mixed>  $data
     */
    protected function carryOverFreeTierConnectionsOnFirstPaidActivation(CrudModel $existing, array &$data): void
    {
        $payment = PackageSubscriptionPaymentStatus::tryFrom((string) ($data['payment_status'] ?? ''));
        if ($payment !== PackageSubscriptionPaymentStatus::Paid) {
            return;
        }

        $wasPaid = $existing->payment_status === PackageSubscriptionPaymentStatus::Paid;
        if ($wasPaid || $existing->isFreeTierCatalogSubscription()) {
            return;
        }

        $existing->loadMissing('snapshot');

        $freeTierSubs = CrudModel::query()
            ->where('user_id', $existing->user_id)
            ->whereKeyNot($existing->id)
            ->activeFreeTierSubscription()
            ->get();

        if ($freeTierSubs->isEmpty()) {
            return;
        }

        $bonus = (int) $freeTierSubs->sum(fn (CrudModel $sub) => max(0, (int) ($sub->remaining_connections ?? 0)));

        $base = $data['remaining_connections'] ?? $existing->remaining_connections;
        if ($base === null) {
            $base = $existing->snapshot?->connections_count ?? 0;
        }
        $data['remaining_connections'] = (int) $base + $bonus;

        foreach ($freeTierSubs as $freeTierSub) {
            $freeTierSub->update([
                'remaining_connections' => 0,
                'status' => PackageSubscriptionStatus::Cancelled,
                'cancelled_at' => $freeTierSub->cancelled_at ?? now(),
            ]);
        }
    }

    protected function notifySubscriberAboutSubscriptionUpdate(User $user, CrudModel $subscription): void
    {
        try {
            $packageName = $subscription->snapshot?->smartTransName() ?? '#'.$subscription->id;

            $titles = [];
            $bodies = [];

            foreach (LaravelLocalization::getSupportedLocales() as $locale => $_props) {
                $statusLabel = trans('admin::cruds.package_subscriptions.statuses.'.$subscription->status->value, [], $locale);
                $paymentLabel = trans('admin::cruds.package_subscriptions.payment_statuses.'.$subscription->payment_status->value, [], $locale);
                $titles[$locale] = trans('admin::cruds.package_subscriptions.notifications.combined_title', [], $locale);
                $bodies[$locale] = trans('admin::cruds.package_subscriptions.notifications.combined_body', [
                    'package' => $packageName,
                    'status' => $statusLabel,
                    'payment_status' => $paymentLabel,
                ], $locale);
            }

            app(NotificationService::class)->sendToUsers([$user->id], [
                'group' => config('admin.main_roles.users'),
                'channels' => [NotificationChannels::FCM_MOBILE, NotificationChannels::FCM_WEB],
                'priority' => NotificationPriority::DEFAULT,
                'title' => $titles,
                'body' => $bodies,
                'long_template' => [],
            ]);
        } catch (\Throwable $e) {
            Log::error('Package subscription subscriber notification failed', [
                'subscription_id' => $subscription->id,
                'user_id' => $user->id,
                'exception' => $e,
            ]);
        }
    }

    public function getDataTable(array $data): JsonResponse
    {
        $model = CrudModel::query()
            ->with(['user', 'snapshot']);

        if (! empty($data['scoped_user_id'])) {
            $model->where('user_id', (int) $data['scoped_user_id']);
        }

        if (! empty($data['exclude_subscription_id'])) {
            $model->where('id', '!=', (int) $data['exclude_subscription_id']);
        }

        if ($this->hasWithDisabled()) {
            $model = $model->withDisabled();
        }

        return DataTables::of($model)
            ->filter(function ($query) use ($data) {
                if (isset($data['search']['value']) && ! empty($data['search']['value'])) {
                    $query->simpleSearch($data['search']['value']);
                }
                if (isset($data['advanced_search']) && ! empty($data['advanced_search'])) {
                    $query->advancedSearch($data['advanced_search']);
                }
            })
            ->addColumn('user_avatar_url', function (CrudModel $row) {
                return $row->user?->image_url ?? asset('images/default/avatars/user.png');
            })
            ->addColumn('user_full_name', function (CrudModel $row) {
                return $row->user ? trim($row->user->full_name) : '';
            })
            ->addColumn('user_email', function (CrudModel $row) {
                return $row->user?->email ?? '';
            })
            ->addColumn('package_name', function (CrudModel $row) {
                return $row->snapshot ? ($row->snapshot->smartTransName() ?? '—') : '—';
            })
            ->addColumn('price_display', function (CrudModel $row) {
                return $row->snapshot ? $row->snapshot->priceDisplay() : '—';
            })
            ->addColumn('status_badge', function (CrudModel $row) {
                return [
                    'label' => $row->status_label,
                    'color' => $row->status->datatableBadgeColor(),
                ];
            })
            ->addColumn('payment_status_badge', function (CrudModel $row) {
                return [
                    'label' => $row->payment_status_label,
                    'color' => $row->payment_status->datatableBadgeColor(),
                ];
            })
            ->addColumn('payment_method_format', function (CrudModel $row) {
                return $row->payment_method_format;
            })
            ->addColumn('actions', function (CrudModel $model) {
                $excludeActions = [HARD_DELETE_ACTION, UPDATE_ACTION];

                return app('customDataTable')
                    ->routePrefix('platform.package_subscriptions')
                    ->of($model, PackageSubscriptionPermissions::PERMISSION_NAMESPACE)
                    ->excludeActions($excludeActions)
                    ->getDatatableActions();
            })
            ->toJson();
    }
}
