<?php

namespace Modules\Front\Http\Controllers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Auth\Models\User;
use Modules\Base\Http\Controllers\BaseController;
use Modules\Front\Http\Requests\ProviderRequestPackageSubscriptionRequest;
use Modules\Platform\Enums\PackageSubscriptionPaymentMethod;
use Modules\Platform\Enums\PackageSubscriptionPaymentStatus;
use Modules\Platform\Enums\PackageSubscriptionStatus;
use Modules\Platform\Http\Services\PackageSubscriptionService;
use Modules\Platform\Models\Package;
use Modules\Platform\Models\PackageSubscription;
use Modules\Verimor\Models\VerimorCallEvent;

class ProviderDashboardController extends BaseController
{
    public function __construct(protected PackageSubscriptionService $packageSubscriptionService)
    {
        parent::__construct();
    }

    public function dashboard()
    {
        $user = Auth::guard('web')->user();
        $user->load([
            'currentPackageSubscription.snapshot',
            'service.translations',
        ]);

        $subscriptionHistory = $this->subscriptionHistoryFor($user, 1);

        $callLog = $this->callLogFor($user, 1);

        $providerStats = [
            'total_calls' => VerimorCallEvent::query()->where('user_id', $user->id)->count(),
            'answered_calls' => VerimorCallEvent::query()->where('user_id', $user->id)->where('answered', true)->count(),
            'subscriptions_count' => $user->packageSubscriptions()->count(),
        ];

        $paidPackages = collect();
        if ($user->service_id !== null) {
            $paidPackages = Package::query()
                ->where('is_free_tier', false)
                ->whereHas(
                    'services',
                    fn ($q) => $q->where('services.id', $user->service_id)
                )
                ->with('translations')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();
        }

        $bankInstructions = (string) getSetting('provider_bank_transfer_instructions', '');
        $whatsappSetting = (string) getSetting('provider_subscription_whatsapp_e164', '');
        $whatsappDigits = preg_replace('/\D+/', '', $whatsappSetting) ?? '';

        $hasPendingSubscriptionPaymentRequest = $user->packageSubscriptions()
            ->pendingNonFreeTierPaymentRequest()
            ->exists();
        $pendingSubscriptionWhatsAppUrl = $hasPendingSubscriptionPaymentRequest
            ? $this->whatsappUrlForLatestPendingNonFreeTierRequest($user, $whatsappDigits)
            : null;

        $showFreeTierConnectionCarryoverNotice = false;
        $freeTierConnectionCarryoverRemaining = 0;
        $currentForCarryoverNotice = $user->currentPackageSubscription;
        if ($currentForCarryoverNotice && $currentForCarryoverNotice->isFreeTierCatalogSubscription()) {
            $showFreeTierConnectionCarryoverNotice = true;
            $freeTierConnectionCarryoverRemaining = (int) ($currentForCarryoverNotice->remaining_connections ?? 0);
        }

        return view('front::provider.auth.dashboard', [
            'providerUser' => $user,
            'subscriptionHistory' => $subscriptionHistory,
            'callLog' => $callLog,
            'providerStats' => $providerStats,
            'paidPackages' => $paidPackages,
            'bankInstructions' => $bankInstructions,
            'whatsappDigitsConfigured' => $whatsappDigits !== '',
            'hasPendingSubscriptionPaymentRequest' => $hasPendingSubscriptionPaymentRequest,
            'pendingSubscriptionWhatsAppUrl' => $pendingSubscriptionWhatsAppUrl,
            'showFreeTierConnectionCarryoverNotice' => $showFreeTierConnectionCarryoverNotice,
            'freeTierConnectionCarryoverRemaining' => $freeTierConnectionCarryoverRemaining,
        ]);
    }

    public function subscriptionHistoryFragment(Request $request)
    {
        $data = $request->validate([
            'page' => ['required', 'integer', 'min:1', 'max:10000'],
        ]);

        $user = Auth::guard('web')->user();
        abort_unless($user instanceof User, 403);

        $subscriptionHistory = $this->subscriptionHistoryFor($user, $data['page']);

        return response()->json([
            'html' => view('front::provider.auth.partials.subscription-history-paginated', [
                'subscriptionHistory' => $subscriptionHistory,
            ])->render(),
        ]);
    }

    public function callLogFragment(Request $request)
    {
        $data = $request->validate([
            'page' => ['required', 'integer', 'min:1', 'max:10000'],
        ]);

        $user = Auth::guard('web')->user();
        abort_unless($user instanceof User, 403);

        $callLog = $this->callLogFor($user, $data['page']);

        return response()->json([
            'html' => view('front::provider.auth.partials.call-log-paginated', [
                'callLog' => $callLog,
            ])->render(),
        ]);
    }

    public function requestPackageSubscription(ProviderRequestPackageSubscriptionRequest $request)
    {
        $user = Auth::guard('web')->user();
        $packageId = (int) $request->validated('package_id');
        Package::query()->findOrFail($packageId);

        $this->packageSubscriptionService->createModel([
            'user_id' => $user->id,
            'package_id' => $packageId,
            'status' => PackageSubscriptionStatus::PendingPayment->value,
            'payment_status' => PackageSubscriptionPaymentStatus::AwaitingVerification->value,
            'payment_method' => PackageSubscriptionPaymentMethod::BankTransfer->value,
        ]);

        return redirect()
            ->route('front.provider.dashboard')
            ->with('success', __('front::provider_dashboard.subscription_requested_flash'));
    }

    private function whatsappUrlForLatestPendingNonFreeTierRequest(User $user, string $whatsappDigits): ?string
    {
        if ($whatsappDigits === '') {
            return null;
        }

        $subscription = $user->packageSubscriptions()
            ->pendingNonFreeTierPaymentRequest()
            ->orderByDesc('id')
            ->first();

        if (! $subscription instanceof PackageSubscription) {
            return null;
        }

        return $this->whatsappUrlForBankTransferSubscription($user, $subscription, $whatsappDigits);
    }

    private function whatsappUrlForBankTransferSubscription(User $user, PackageSubscription $subscription, string $whatsappDigits): string
    {
        $subscription->loadMissing('snapshot');
        $packageName = $subscription->snapshot?->smartTransName() ?? (string) $subscription->id;
        $bankInstructions = (string) getSetting('provider_bank_transfer_instructions', '');

        $messageLines = [
            __('front::provider_dashboard.whatsapp_message_heading'),
            __('front::provider_dashboard.whatsapp_message_body', [
                'name' => $user->full_name,
                'email' => $user->email,
                'package' => $packageName,
                'subscription_id' => (string) $subscription->id,
            ]),
        ];
        if ($bankInstructions !== '') {
            $messageLines[] = $bankInstructions;
        }
        $message = implode("\n\n", array_filter($messageLines));

        return 'https://wa.me/'.$whatsappDigits.'?text='.rawurlencode($message);
    }

    private function subscriptionHistoryFor(User $user, int $page): LengthAwarePaginator
    {
        return $user->packageSubscriptions()
            ->with('snapshot')
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'page', $page);
    }

    private function callLogFor(User $user, int $page): LengthAwarePaginator
    {
        return VerimorCallEvent::query()
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(15, ['*'], 'page', $page);
    }
}
