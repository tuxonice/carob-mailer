@props(['status'])

@if ($status)
    <div {{ $attributes }}>
        <div class="alert alert-info alert-dismissible">
            {{ $status }}
        </div>
    </div>
@endif
