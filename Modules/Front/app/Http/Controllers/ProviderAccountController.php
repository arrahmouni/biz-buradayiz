<?php

namespace Modules\Front\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Modules\Auth\Http\Requests\ChangePasswordRequest;
use Modules\Auth\Http\Services\ProfileService;
use Modules\Auth\Models\User;
use Modules\Base\Http\Controllers\BaseController;
use Modules\Front\Http\Requests\ProviderAccountUpdateRequest;
use Modules\Front\Support\FrontPublicServices;

class ProviderAccountController extends BaseController
{
    public function __construct(private ProfileService $profileService)
    {
        parent::__construct();
    }

    public function edit(): View
    {
        $user = Auth::guard('web')->user();
        abort_unless($user instanceof User, 403);
        $user->loadMissing(['city.state', 'service']);

        return view('front::provider.auth.account', [
            'providerUser' => $user,
            'registerServices' => FrontPublicServices::forSearchFilters(),
        ]);
    }

    public function update(ProviderAccountUpdateRequest $request): RedirectResponse
    {
        $user = Auth::guard('web')->user();
        abort_unless($user instanceof User, 403);

        $data = $request->validated();

        DB::transaction(function () use ($user, $data, $request): void {
            $this->profileService->updateProfile(
                $user,
                Arr::only($data, [
                    'first_name',
                    'last_name',
                    'company_name',
                    'email',
                    'service_id',
                    'city_id',
                ])
            );

            if ($request->hasFile('personal_photo')) {
                $user->getFirstMedia(User::MEDIA_COLLECTION)?->delete();
                $user->addMediaFromRequest('personal_photo')->toMediaCollection(User::MEDIA_COLLECTION);
            }

            if ($request->hasFile('service_image')) {
                $user->getFirstMedia(User::SERVICE_IMAGE_MEDIA_COLLECTION)?->delete();
                $user->addMediaFromRequest('service_image')->toMediaCollection(User::SERVICE_IMAGE_MEDIA_COLLECTION);
            }
        });

        return redirect()
            ->route('front.provider.account')
            ->with('success', __('front::provider_account.profile_updated'));
    }

    public function updatePassword(ChangePasswordRequest $request): RedirectResponse
    {
        $user = Auth::guard('web')->user();
        abort_unless($user instanceof User, 403);

        $response = $this->profileService->changePassword($user, $request->validated());

        if (empty($response['success'])) {
            $messageKey = $response['message'] ?? '';
            if (is_array($messageKey)) {
                $messageKey = $messageKey[0] ?? '';
            }
            $messageKey = (string) $messageKey;

            $errorMessage = match (true) {
                str_contains($messageKey, 'old_password_is_incorrect') => __('front::provider_account.password_old_incorrect'),
                str_contains($messageKey, 'new_password_cannot_be_same_as_old_password') => __('front::provider_account.password_new_same_as_old'),
                default => __('front::provider_account.password_change_failed'),
            };

            return redirect()
                ->back()
                ->withErrors(['old_password' => $errorMessage])
                ->withInput($request->only('old_password'));
        }

        return redirect()
            ->route('front.provider.account')
            ->with('success', __('front::provider_account.password_updated'));
    }
}
