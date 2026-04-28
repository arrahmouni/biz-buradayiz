<?php

namespace Modules\Config\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Base\Http\Requests\BaseRequest;

class UpdateSettingRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $generalSettingRules = [
            'app_name' => ['required', 'array'],
            'app_name.*' => ['required', 'string', 'max:255'],
            'app_default_language' => ['required', 'in:'.implode(',', array_values(LaravelLocalization::getSupportedLanguagesKeys()))],
            'maintenance_mode' => ['required', 'boolean'],
            'coming_soon_mode' => ['required', 'boolean'],
            'website_launch_date' => [
                'nullable',
                Rule::requiredIf(fn () => $this->boolean('coming_soon_mode')),
                'date',
                'after:now',
            ],
        ];

        $socialSettingRules = [
            'facebook' => ['nullable', 'url', 'max:255'],
            'twitter' => ['nullable', 'url', 'max:255'],
            'instagram' => ['nullable', 'url', 'max:255'],
            'linkedin' => ['nullable', 'url', 'max:255'],
            'youtube' => ['nullable', 'url', 'max:255'],
            'tiktok' => ['nullable', 'url', 'max:255'],
        ];

        $mobileSettingRules = [
            'app_store' => ['nullable', 'url', 'max:255'],
            'google_play' => ['nullable', 'url', 'max:255'],
        ];

        $contactSettingRules = [
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
        ];

        $platformSettingRules = [
            'emergency_contact_number' => ['nullable', 'string', 'max:255'],
            'front_search_default_country_id' => ['nullable', 'integer', 'exists:countries,id'],
            'provider_register_landing_youtube_url' => ['nullable', 'url', 'max:2048'],
        ];

        $mediaSettingRules = [
            'app_logo' => ['nullable', 'image', File::image(allowSvg: true)
                ->types(config('config.app_logo.types'))
                ->max(config('config.app_logo.max_size').'mb'),
            ],
            'app_mobile_logo' => ['nullable', 'image', File::image(allowSvg: true)
                ->types(config('config.app_logo.types'))
                ->max(config('config.app_logo.max_size').'mb'),
            ],
            'app_favicon' => ['nullable', 'image', File::image(allowSvg: true)
                ->types(config('config.app_favicon.types'))
                ->max(config('config.app_favicon.max_size').'mb'),
            ],
            'email_logo' => ['nullable', 'image', File::image(allowSvg: true)
                ->types(config('config.app_logo.types'))
                ->max(config('config.app_logo.max_size').'mb'),
            ],
            'front_hero_background' => ['nullable', 'image', File::image(allowSvg: true)
                ->types(config('config.app_logo.types'))
                ->max(config('config.app_logo.max_size').'mb'),
            ],
            'app_placeholder' => ['nullable', 'image', File::image(allowSvg: true)->types(config('base.file.image.accepted_types'))->max('2mb')],
        ];

        $providerRankingSettingRules = [
            'featured_providers_count' => ['required', 'integer', 'min:1'],
            'new_provider_hours' => ['required', 'integer', 'min:1'],
            'ranking_weight_rating' => ['required', 'integer', 'min:0', 'max:100'],
            'ranking_weight_activity' => ['required', 'integer', 'min:0', 'max:100'],
            'ranking_weight_experience' => ['required', 'integer', 'min:0', 'max:100'],
        ];

        $developerSettingRules = app('owner') ? [
            'session_lifetime' => ['required', 'integer', 'min:60'],
            'allow_debug_for_custom_ip' => ['required', 'boolean'],
            'custom_ips' => ['nullable', 'string', 'regex:/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})(,\s*\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})*$/'],
            'verimor_webhook_allowed_ips' => ['nullable', 'string', 'regex:/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})(,\s*\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})*$/'],
        ] : [];

        return array_merge($generalSettingRules, $socialSettingRules, $mobileSettingRules, $contactSettingRules, $platformSettingRules, $mediaSettingRules, $developerSettingRules, $providerRankingSettingRules);
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'website_launch_date.required' => trans('config::settings.validation.website_launch_date_required_when_coming_soon'),
        ];
    }

    public function after(): array
    {
        return [
            function ($validator) {
                if (isset($this->custom_ip) && ! empty($this->custom_ip)) {
                    $ips = explode(',', $this->custom_ip);
                    foreach ($ips as $ip) {
                        if (! filter_var(trim($ip), FILTER_VALIDATE_IP)) {
                            $validator->errors()->add($this->custom_ip, trans('validation.ip', ['attribute' => $this->custom_ip]));
                        }
                    }
                }

                $weights = [
                    $this->input('ranking_weight_rating', 0),
                    $this->input('ranking_weight_activity', 0),
                    $this->input('ranking_weight_experience', 0),
                ];

                if (array_sum($weights) > 100) {
                    $validator->errors()->add('ranking_weight_rating', trans('config::settings.ranking_weights_sum_exceeded'));
                }
            },
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
