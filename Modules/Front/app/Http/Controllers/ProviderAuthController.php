<?php

namespace Modules\Front\Http\Controllers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Modules\Admin\Enums\AdminStatus;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Models\User;
use Modules\Base\Http\Controllers\BaseController;
use Modules\Front\Http\Requests\ProviderForgotPasswordRequest;
use Modules\Front\Http\Requests\ProviderLoginRequest;
use Modules\Front\Http\Requests\ProviderRegisterRequest;
use Modules\Front\Http\Requests\ProviderRequestPackageSubscriptionRequest;
use Modules\Front\Http\Requests\ProviderResetPasswordRequest;
use Modules\Front\Support\FrontPublicServices;
use Modules\Notification\Http\Services\NotificationService;
use Modules\Platform\Enums\PackageSubscriptionPaymentMethod;
use Modules\Platform\Enums\PackageSubscriptionPaymentStatus;
use Modules\Platform\Enums\PackageSubscriptionStatus;
use Modules\Platform\Http\Services\PackageSubscriptionService;
use Modules\Platform\Models\Package;
use Modules\Platform\Models\Service;
use Modules\Verimor\Models\VerimorCallEvent;

class ProviderAuthController extends BaseController
{
    public function showLoginForm()
    {
        return view('front::provider.auth.login');
    }

    public function login(ProviderLoginRequest $request)
    {
        $data = $request->validated();

        $user = User::query()->where('email', $data['email'])->first();

        if (! $user || $user->type !== UserType::ServiceProvider) {
            return sendFailResponse(customMessage: __('front::auth.invalid_credentials'));
        }

        if (! Hash::check($data['password'], $user->password)) {
            return sendFailResponse(customMessage: __('front::auth.invalid_credentials'));
        }

        if (! $user->isActive()) {
            return sendFailResponse(customMessage: __('front::auth.pending_account'));
        }

        Auth::guard('web')->login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        return sendSuccessResponse(route('front.provider.dashboard'), 'login_success', true);
    }

    public function showRegisterLanding()
    {
        $this->data['servicesWithPackages'] = Service::query()
            ->forSearchFilters()
            ->whereHas('packages', fn ($q) => $q->where('packages.is_free_tier', false))
            ->with([
                'packages' => fn ($q) => $q
                    ->where('is_free_tier', false)
                    ->orderBy('sort_order')
                    ->orderBy('id'),
            ])
            ->orderBy('id')
            ->get()
            ->each(function (Service $service): void {
                $packages = $service->packages;
                $popular = $packages->firstWhere('is_popular', true);
                if (! $popular) {
                    return;
                }
                $others = $packages->filter(fn ($p) => ! $p->is_popular)->values();
                $ordered = $others->isEmpty()
                    ? collect([$popular])
                    : collect([$others->first(), $popular])->concat($others->slice(1));
                $service->setRelation('packages', $ordered);
            });

        return view('front::provider.auth.register-landing', $this->data);
    }

    public function showRegisterForm()
    {
        return view('front::provider.auth.register', [
            'registerServices' => FrontPublicServices::forSearchFilters(),
        ]);
    }

    public function register(ProviderRegisterRequest $request)
    {
        $data = $request->validated();

        User::query()->create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'password' => $data['password'],
            'type' => UserType::ServiceProvider,
            'status' => AdminStatus::PENDING,
            'lang' => app()->getLocale(),
            'service_id' => $data['service_id'],
            'city_id' => $data['city_id'],
        ]);

        return redirect()
            ->route('front.provider.login')
            ->with('success', __('front::auth.register_success_pending'));
    }

    public function showForgotPasswordForm()
    {
        return view('front::provider.auth.forgot-password');
    }

    public function sendResetLinkEmail(ProviderForgotPasswordRequest $request)
    {
        $status = Password::broker('users')->sendResetLink(
            $request->only('email'),
            function (User $user, string $token) {
                $resetLink = route('front.provider.password.reset', [
                    'token' => $token,
                    'email' => $user->getEmailForPasswordReset(),
                ], absolute: true);

                app(NotificationService::class)->send($user, 'forget_password', [
                    'username' => $user->full_name,
                    'reset_link' => $resetLink,
                ]);

                return Password::RESET_LINK_SENT;
            }
        );

        if ($status === Password::RESET_LINK_SENT) {
            return redirect()
                ->route('front.provider.password.request')
                ->with('status', __($status));
        }

        return redirect()
            ->route('front.provider.password.request')
            ->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('front::provider.auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(ProviderResetPasswordRequest $request)
    {
        $status = Password::broker('users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => $password,
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('front.provider.login')
                ->with('success', __($status));
        }

        return redirect()
            ->back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('front.index')
            ->with('status', __('front::auth.logout_success'));
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

        return view('front::provider.auth.dashboard', [
            'providerUser' => $user,
            'subscriptionHistory' => $subscriptionHistory,
            'callLog' => $callLog,
            'providerStats' => $providerStats,
            'paidPackages' => $paidPackages,
            'bankInstructions' => $bankInstructions,
            'whatsappDigitsConfigured' => $whatsappDigits !== '',
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

    public function requestPackageSubscription(
        ProviderRequestPackageSubscriptionRequest $request,
        PackageSubscriptionService $packageSubscriptionService
    ) {
        $user = Auth::guard('web')->user();
        $packageId = (int) $request->validated('package_id');
        $package = Package::query()->with('translations')->findOrFail($packageId);

        $subscription = $packageSubscriptionService->createModel([
            'user_id' => $user->id,
            'package_id' => $packageId,
            'status' => PackageSubscriptionStatus::PendingPayment->value,
            'payment_status' => PackageSubscriptionPaymentStatus::AwaitingVerification->value,
            'payment_method' => PackageSubscriptionPaymentMethod::BankTransfer->value,
            'admin_notes' => 'Provider self-service request from dashboard.',
        ]);

        $packageName = $package->smartTrans('name') ?? (string) $package->id;
        $bankInstructions = (string) getSetting('provider_bank_transfer_instructions', '');
        $whatsappSetting = (string) getSetting('provider_subscription_whatsapp_e164', '');
        $whatsappDigits = preg_replace('/\D+/', '', $whatsappSetting) ?? '';

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

        $whatsappUrl = $whatsappDigits !== ''
            ? 'https://wa.me/'.$whatsappDigits.'?text='.rawurlencode($message)
            : null;

        return redirect()
            ->route('front.provider.dashboard')
            ->with('success', __('front::provider_dashboard.subscription_requested_flash'))
            ->with('subscription_whatsapp_url', $whatsappUrl);
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
