<div
    {{ $attributes->merge(['class' => 'alert '. $type]) }}
    role="alert"
>
    {{ $slot }}
</div>
