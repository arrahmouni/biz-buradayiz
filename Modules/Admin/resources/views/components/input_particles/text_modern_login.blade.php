@php
    $fieldName = $VALUE['name'];
    $inputValue = old($fieldName, $VALUE['value']);
    $hasError = $errors->has($fieldName);
@endphp
<div class="modern-form-group">
    <div class="modern-input-wrapper">
        <svg class="modern-input-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M10 10C12.7614 10 15 7.76142 15 5C15 2.23858 12.7614 0 10 0C7.23858 0 5 2.23858 5 5C5 7.76142 7.23858 10 10 10Z" fill="currentColor"/>
            <path d="M10 12C4.47715 12 0 14.6863 0 18V20H20V18C20 14.6863 15.5228 12 10 12Z" fill="currentColor"/>
        </svg>
        <input
            title=""
            id="{{ $VALUE['id'] }}"
            type="{{ $VALUE['type'] }}"
            class="modern-input{{ $hasError ? ' is-invalid' : '' }}"
            name="{{ $VALUE['name'] }}"
            value="{{ $inputValue }}"
            @required($VALUE['required'])
            @readonly($VALUE['readonly'])
            @disabled($VALUE['disabled'])
            autocomplete="{{ $VALUE['type'] === 'email' ? 'email' : 'on' }}"
            @if($VALUE['type'] === 'email') autofocus @endif
            placeholder=" "
            @if($VALUE['maxlength']) maxlength="{{ $VALUE['maxlength'] }}" @endif
            inputmode="{{ $VALUE['inputmode'] }}"
        />
        @isset($VALUE['label'])
            <label for="{{ $VALUE['id'] }}" class="modern-label">{{ $VALUE['label'] }}</label>
        @endisset
    </div>
    @error($fieldName)
        <span class="modern-error" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>
