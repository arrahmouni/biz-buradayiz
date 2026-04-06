@php
    $fieldName = $VALUE['name'];
    $hasError = $errors->has($fieldName);
@endphp
<div class="modern-form-group" @if($VALUE['visibleToggle']) data-kt-password-meter="true" @endif>
    <div class="modern-input-wrapper modern-input-wrapper--password">
        <svg class="modern-input-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M15 7H14V5C14 2.24 11.76 0 9 0C6.24 0 4 2.24 4 5V7H3C1.9 7 1 7.9 1 9V18C1 19.1 1.9 20 3 20H15C16.1 20 17 19.1 17 18V9C17 7.9 16.1 7 15 7ZM9 2C10.66 2 12 3.34 12 5V7H6V5C6 3.34 7.34 2 9 2ZM15 18H3V9H15V18ZM9 14C10.1 14 11 13.1 11 12C11 10.9 10.1 10 9 10C7.9 10 7 10.9 7 12C7 13.1 7.9 14 9 14Z" fill="currentColor"/>
        </svg>
        <input
            title=""
            id="{{ $VALUE['id'] }}"
            type="password"
            class="modern-input{{ $hasError ? ' is-invalid' : '' }}"
            name="{{ $VALUE['name'] }}"
            @required($VALUE['required'])
            @readonly($VALUE['readonly'])
            @disabled($VALUE['disabled'])
            autocomplete="current-password"
            placeholder=" "
            inputmode="{{ $VALUE['inputmode'] }}"
        />
        @isset($VALUE['label'])
            <label for="{{ $VALUE['id'] }}" class="modern-label">{{ $VALUE['label'] }}</label>
        @endisset
        @if($VALUE['visibleToggle'])
            <span
                class="modern-password-toggle"
                data-kt-password-meter-control="visibility"
                role="button"
                tabindex="0"
                aria-label="{{ trans('admin::auth.login_page.toggle_password_visibility') }}"
            >
                <i class="bi bi-eye-slash fs-5"></i>
                <i class="bi bi-eye fs-5 d-none"></i>
            </span>
        @endif
    </div>
    @error($fieldName)
        <span class="modern-error" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>
