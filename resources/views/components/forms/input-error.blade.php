@error($name)
<p
    role="alert"
    {{ $attributes->merge(['class' => 'text-error text-sm']) }}
>
    {{ $message }}
</p>
@enderror
