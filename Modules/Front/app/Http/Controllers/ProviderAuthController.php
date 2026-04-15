<?php

namespace Modules\Front\Http\Controllers;

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
use Modules\Front\Http\Requests\ProviderResetPasswordRequest;
use Modules\Front\Support\FrontPublicServices;
use Modules\Notification\Http\Services\NotificationService;
use Modules\Platform\Models\Service;

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
}
