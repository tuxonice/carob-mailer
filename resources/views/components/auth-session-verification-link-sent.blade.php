@props(['status'])

@if ($status == 'verification-link-sent')
    <div {{ $attributes }}>
        <div class="alert alert-info alert-dismissible">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    </div>
@endif

