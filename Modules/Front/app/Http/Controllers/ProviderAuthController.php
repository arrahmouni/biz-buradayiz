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
            $request->only('email')
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
            ->route('front.provider.login')
            ->with('status', __('front::auth.logout_success'));
    }

    public function dashboard()
    {
        $user = Auth::guard('web')->user();

        return view('front::provider.auth.dashboard', [
            'providerUser' => $user,
        ]);
    }
}
