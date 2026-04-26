@php
    $spAcceptServiceProviderBody = view('auth::users.partials.service-provider-accept-approval-modal-body')->render();
@endphp
@include('admin::components.modals.modal', [
    'options' => [
        'id' => 'spAcceptServiceProviderModal',
    ],
    'body' => $spAcceptServiceProviderBody,
])
