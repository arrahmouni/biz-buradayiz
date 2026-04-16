<?php

namespace Modules\Auth\Http\Services;

use Illuminate\Support\Facades\Hash;
use Modules\Auth\Enums\UserType;
use Modules\Base\Http\Services\BaseService;

class ProfileService extends BaseService
{
    public function changeLanguage($user, $data)
    {
        $user->update($data);

        return sendSuccessInternalResponse('language_changed_successfully');
    }

    public function changePassword($user, $data)
    {
        if (! Hash::check($data['old_password'], $user->password)) {
            return sendFailInternalResponse('old_password_is_incorrect');
        }

        if (Hash::check($data['new_password'], $user->password)) {
            return sendFailInternalResponse('new_password_cannot_be_same_as_old_password');
        }

        $user->update([
            'password' => $data['new_password'],
        ]);

        return sendSuccessInternalResponse('password_changed_successfully');
    }

    public function updateProfile($user, $data)
    {
        $previousFirstName = $user->first_name;
        $previousLastName = $user->last_name;

        $user->update($data);

        if ($user->type === UserType::ServiceProvider
            && (($user->first_name !== $previousFirstName) || ($user->last_name !== $previousLastName))
        ) {
            // `preventOverwrite()` on the model blocks automatic slug refresh on update; force regeneration
            // so the public profile URL stays aligned with the display name. Old URLs are not redirected.
            $user->generateSlug();
            if ($user->isDirty('profile_slug')) {
                $user->save();
            }
        }

        return sendSuccessInternalResponse('profile_updated_successfully');
    }
}
