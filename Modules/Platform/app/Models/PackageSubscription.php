<?php

namespace Modules\Platform\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Auth\Models\User;
use Modules\Base\Models\BaseModel;
use Modules\Platform\Database\Factories\PackageSubscriptionFactory;
use Modules\Platform\Enums\BillingPeriod;
use Modules\Platform\Enums\PackageSubscriptionPaymentMethod;
use Modules\Platform\Enums\PackageSubscriptionPaymentStatus;
use Modules\Platform\Enums\PackageSubscriptionStatus;
use Modules\Verimor\Jobs\ProcessVerimorCrmWebhookJob;

class PackageSubscription extends BaseModel
{
    use HasFactory;

    const VIEW_PATH = 'package_subscriptions';

    protected $fillable = [
        'user_id',
        'status',
        'payment_status',
        'payment_method',
        'starts_at',
        'ends_at',
        'cancelled_at',
        'paid_at',
        'remaining_connections',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => PackageSubscriptionStatus::class,
            'payment_status' => PackageSubscriptionPaymentStatus::class,
            'payment_method' => PackageSubscriptionPaymentMethod::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    protected $appends = [
        'created_at_format',
        'status_label',
        'payment_status_label',
        'payment_method_label',
        'payment_method_format',
        'starts_at_display',
        'paid_at_display',
    ];

    protected static function newFactory(): PackageSubscriptionFactory
    {
        return PackageSubscriptionFactory::new();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function snapshot(): HasOne
    {
        return $this->hasOne(PackageSubscriptionSnapshot::class);
    }

    /**
     * True when this subscription was created from the catalog free-tier package (including if that package was later soft-deleted).
     */
    public function isFreeTierCatalogSubscription(): bool
    {
        $this->loadMissing('snapshot');
        $sourcePackageId = $this->snapshot?->source_package_id;
        if ($sourcePackageId === null) {
            return false;
        }

        return (bool) Package::withTrashed()->whereKey($sourcePackageId)->value('is_free_tier');
    }

    /**
     * Subscription end instant from catalog billing period (snapshot source), relative to start.
     */
    public static function calculateEndsAtFromPackageBilling(Package $package, Carbon $startsAt): ?Carbon
    {
        $billing = $package->billing_period;
        if (! $billing instanceof BillingPeriod) {
            $raw = $package->getRawOriginal('billing_period');
            $billing = BillingPeriod::tryFrom((string) $raw) ?? BillingPeriod::OneTime;
        }

        return match ($billing) {
            BillingPeriod::Monthly => $startsAt->copy()->addMonth(),
            BillingPeriod::Yearly => $startsAt->copy()->addYear(),
            BillingPeriod::OneTime => null,
        };
    }

    public function scopeSimpleSearch($query, $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('package_subscriptions.id', $search)
                ->orWhereHas('user', function ($q) use ($search) {
                    $q->where('email', 'like', '%'.$search.'%')
                        ->orWhere('first_name', 'like', '%'.$search.'%')
                        ->orWhere('last_name', 'like', '%'.$search.'%')
                        ->orWhere('phone_number', 'like', '%'.$search.'%')
                        ->orWhere('central_phone', 'like', '%'.$search.'%');
                })
                ->orWhereHas('snapshot', function ($q) use ($search) {
                    $like = '%'.$search.'%';
                    $q->where('name_translations', 'like', $like)
                        ->orWhere('currency', 'like', $like)
                        ->orWhere('price', 'like', $like);
                });
        });
    }

    public function scopeAdvancedSearch($query, $search)
    {
        return $query
            ->when(! empty($search['status']), fn ($q) => $q->where('status', $search['status']))
            ->when(! empty($search['payment_status']), fn ($q) => $q->where('payment_status', $search['payment_status']))
            ->when(! empty($search['payment_method']), fn ($q) => $q->where('payment_method', $search['payment_method']));
    }

    /**
     * Active package: active status, paid, remaining connections, and not expired (null ends_at = no expiry).
     */
    public function scopeActiveSubscription($query)
    {
        return $query
            ->where('status', PackageSubscriptionStatus::Active)
            ->where('payment_status', PackageSubscriptionPaymentStatus::Paid)
            ->where('remaining_connections', '>', 0)
            ->where(function ($q) {
                $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            });
    }

    /**
     * Active subscription tied to a non–free-tier catalog package (snapshot source).
     * A provider may still request a paid package while only a free-tier subscription is active.
     *
     * @see ProcessVerimorCrmWebhookJob When multiple rows match activeSubscription(),
     *      the newest id is used first for quota consumption.
     */
    public function scopeActiveNonFreeTierSubscription($query)
    {
        return $query->activeSubscription()
            ->whereHas('snapshot.sourcePackage', fn ($q) => $q->where('is_free_tier', false));
    }

    /**
     * Active subscription tied to the catalog free-tier package (snapshot source).
     */
    public function scopeActiveFreeTierSubscription($query)
    {
        return $query->activeSubscription()
            ->whereHas('snapshot.sourcePackage', fn ($q) => $q->where('is_free_tier', true));
    }

    /**
     * Non–free-tier subscription awaiting bank confirmation (provider self-service or admin draft).
     */
    public function scopePendingNonFreeTierPaymentRequest($query)
    {
        return $query
            ->where('status', PackageSubscriptionStatus::PendingPayment)
            ->whereIn('payment_status', [
                PackageSubscriptionPaymentStatus::Pending,
                PackageSubscriptionPaymentStatus::AwaitingVerification,
            ])
            ->whereHas('snapshot.sourcePackage', fn ($q) => $q->where('is_free_tier', false));
    }

    public function formAjaxArray($selected = true): array
    {
        $this->loadMissing(['user', 'snapshot']);

        $label = '#'.$this->id;
        if ($this->user) {
            $label .= ' — '.$this->user->email;
        }
        if ($this->snapshot) {
            $label .= ' — '.($this->snapshot->smartTransName() ?? '');
        }

        return [
            'id' => $this->id,
            'text' => $label,
            'selected' => $selected,
        ];
    }

    public function createdAtFormat(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
        );
    }

    public function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => trans('admin::cruds.package_subscriptions.statuses.'.$this->status->value),
        );
    }

    public function paymentStatusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => trans('admin::cruds.package_subscriptions.payment_statuses.'.$this->payment_status->value),
        );
    }

    public function paymentMethodLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => trans('admin::cruds.package_subscriptions.payment_methods.'.$this->payment_method->value),
        );
    }

    /**
     * @return array{label: string, img: string}
     */
    public function paymentMethodFormat(): Attribute
    {
        return Attribute::make(
            get: fn () => [
                'label' => trans('admin::cruds.package_subscriptions.payment_methods.'.$this->payment_method->value),
                'img' => $this->payment_method->adminDisplayIconUrl(),
            ],
        );
    }

    public function startsAtDisplay(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->starts_at?->format('Y-m-d H:i') ?? '—',
        );
    }

    public function paidAtDisplay(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->paid_at?->format('Y-m-d H:i') ?? '—',
        );
    }
}
