@isset($options)
    @php
        $VALUE = array_merge([
            'id'                => null,
            'type'              => 'submit',
            'class'             => 'btn-primary w-100',
            'label'             => null,
            'progress_label'    => trans('admin::base.please_wait_dot'),
            'attributes'        => [],
            'variant'           => null,
        ], $options);
        $isModernLogin = $VALUE['variant'] === 'modern_login';
    @endphp

    <button
        type="{{$VALUE['type']}}"
        id="{{$VALUE['id']}}"
        @if($isModernLogin)
            @class(['modern-submit-btn', 'w-100'])
        @else
            @class(['btn', $VALUE['class']])
        @endif
        @foreach($VALUE['attributes'] as $key => $value)
            {{ $key }}="{{ $value }}"
        @endforeach
    >
        <span class="indicator-label">
            {{$VALUE['label']}}
            @if($isModernLogin)
                <svg class="modern-submit-chevron" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path d="M10 0L8.59 1.41L15.17 8H0V10H15.17L8.59 16.59L10 18L20 10L10 0Z" fill="currentColor"/>
                </svg>
            @endif
        </span>
        <span class="indicator-progress">
            {{$VALUE['progress_label'] }}
            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
        </span>
    </button>
@endisset
