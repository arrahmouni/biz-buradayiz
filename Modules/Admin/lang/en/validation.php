<?php

return [
    'at_least_one_locale' => 'Please enter data in at least one language',
    'permission_name_end_with_group_code' => 'The permission name must end with the name of the group to which it belongs. :group_code',
    'phone_code.regex' => 'The phone code must be in the format 99 or +999 or +999-9999',
    'iso2.regex' => 'The ISO2 code must be in the format XX',
    'iso3.regex' => 'The ISO3 code must be in the format XXX',
    'cant_add_fields_without_title' => 'You cannot add content without a title. Please add a title first.',
    'central_phone_regex' => 'Central phone may contain digits only, with an optional single + at the start.',
    'package_must_cover_provider_service' => 'The selected package must include the provider’s service type.',
    'package_subscription' => [
        'active_requires_paid' => 'An active subscription must have payment status set to Paid.',
        'paid_cannot_be_cancelled' => 'A paid subscription cannot have status Cancelled. Change payment status first.',
        'starts_at_required_when_active' => 'A start date is required when the subscription is active or payment is paid.',
        'provider_already_has_active_package' => 'This service provider already has an active package (paid, not expired, with remaining connections).',
        'paid_cannot_be_pending_payment' => 'While payment is Paid, the subscription cannot be set to Pending payment.',
        'paid_cannot_apply_to_cancelled' => 'Payment cannot be marked as Paid while the subscription is cancelled.',
        'paid_requires_active_status' => 'When payment is Paid, subscription status must be Active.',
    ],
];
