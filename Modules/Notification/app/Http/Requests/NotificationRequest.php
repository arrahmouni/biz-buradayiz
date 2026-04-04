<?php

namespace Modules\Notification\Http\Requests;

use Illuminate\Support\Facades\DB;
use Modules\Base\Http\Requests\BaseRequest;
use Modules\Notification\Enums\NotificationChannels;
use Modules\Notification\Enums\NotificationPriority;

class NotificationRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $mainRoles = config('admin.main_roles');
        $rules = [
            'group' => ['required', 'string', 'in:'.implode(',', array_values($mainRoles))],
            'users_id' => ['array'],
            'channels' => ['required', 'array'],
            'channels.*' => ['required', 'in:'.implode(',', NotificationChannels::all())],
            'priority' => ['required', 'in:'.implode(',', NotificationPriority::all())],
            'link' => ['nullable', 'url'],
            'title' => ['required', 'array'],
            'body' => $this->requiresBody() ? ['required', 'array'] : ['nullable', 'array'],
            'long_template' => $this->requiresLongTemplate() ? ['required', 'array'] : ['nullable', 'array'],
        ];

        if ($this->group === $mainRoles['classrooms']) {
            $rules['classroom_id'] = ['required', 'integer', 'exists:classrooms,id'];
        }

        if (isset($this->users_id) && is_array($this->users_id)) {
            $group = $this->group ?? null;
            $classroomId = $this->classroom_id ?? null;
            foreach ($this->users_id as $key => $id) {
                $rules["users_id.$key"] = [
                    'required',
                    'integer',
                    function ($attribute, $value, $fail) use ($group, $mainRoles, $classroomId) {
                        if ($group === $mainRoles['classrooms']) {
                            $existsInUsers = DB::table('users')->where('id', $value)->exists();
                            if (! $existsInUsers) {
                                $fail(trans('notification::validation.users_id_must_exist_in_users', ['attribute' => $attribute]));

                                return;
                            }
                            $query = DB::table('classroom_students')->where('user_id', $value);
                            if ($classroomId) {
                                $query->where('classroom_id', $classroomId);
                            }
                            if (! $query->exists()) {
                                $fail(trans('notification::validation.users_id_must_be_enrolled_in_classroom', ['attribute' => $attribute]));
                            }

                            return;
                        }
                        if ($group === $mainRoles['users']) {
                            $exists = DB::table('users')->where('id', $value)->exists();
                            if (! $exists) {
                                $fail(trans('notification::validation.users_id_must_exist_in_users', ['attribute' => $attribute]));
                            }

                            return;
                        }
                        $existsInAdmins = DB::table('admins')->where('id', $value)->exists();
                        if (! $existsInAdmins) {
                            $fail(trans('notification::validation.users_id_must_exist_in_admins', ['attribute' => $attribute]));
                        }
                    },
                ];
            }
        }

        if (isset($this->channels) && is_array($this->channels)) {
            $fcmChannels = [NotificationChannels::FCM_MOBILE, NotificationChannels::FCM_WEB];
            $hasFcmChannel = count(array_intersect($fcmChannels, $this->channels)) > 0;
            $hasUsersSelected = isset($this->users_id) && is_array($this->users_id) && count($this->users_id) > 0;
            $hasClassroomSelected = $this->group === $mainRoles['classrooms'] && ! empty($this->classroom_id);
            $hasRecipientsSelected = $hasUsersSelected || $hasClassroomSelected;

            if ($hasFcmChannel && ! $hasRecipientsSelected) {
                $rules['channels'] = [
                    'required',
                    'array',
                    function ($attribute, $value, $fail) use ($fcmChannels) {
                        if (array_diff($value, $fcmChannels)) {
                            $fail(trans('notification::validation.fcm_requires_recipients'));
                        }
                    },
                ];
            }
        }

        return $rules;
    }

    public function after(): array
    {
        return [
            function ($validator) {
                $this->validateBaseInput(validator: $validator, data: $this->title, inputName: 'title', atLeastOneLocaleWithSize: true);
                $this->validateBaseInput(validator: $validator, data: $this->body, inputName: 'body', atLeastOneLocaleWithSize: $this->requiresBody(), textarea: true);
                $this->validateBaseInput(validator: $validator, data: $this->long_template, inputName: 'long_template', atLeastOneLocaleWithSize: $this->requiresLongTemplate(), longText: true);

                $cantAcceptFieldsWithoutTitle = [
                    'body',
                    'long_template',
                ];

                $this->validateFieldsWithoutTitle($validator, $this->all(), 'title', $cantAcceptFieldsWithoutTitle);
            },
        ];
    }

    /**
     * @return list<string>
     */
    private function notificationChannels(): array
    {
        return is_array($this->channels) ? $this->channels : [];
    }

    private function requiresLongTemplate(): bool
    {
        return in_array(NotificationChannels::MAIL, $this->notificationChannels(), true);
    }

    private function requiresBody(): bool
    {
        $bodyChannels = [
            NotificationChannels::FCM_MOBILE,
            NotificationChannels::FCM_WEB,
            NotificationChannels::SMS,
        ];

        return count(array_intersect($bodyChannels, $this->notificationChannels())) > 0;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
