<li>
    <a
        href="{{ $url }}"
        {{ $attributes->class(['active' => $active ?? false]) }}
    >{{ $slot }}</a>
</li>
