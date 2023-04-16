<li>
    @if($url ?? null)
        <a href="{{ $url }}">&laquo; {{ $name }}</a>
    @else
        {{ $name }}
    @endif
</li>
