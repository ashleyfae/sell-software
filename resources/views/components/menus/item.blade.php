<li
    {{ $attributes->merge(['class' => 'block md:mr-8 mb-3 md:mb-0 text-center'])->class(['active' => Route::current()->getName() === $routeName ]) }}
>
    <a href="{{ $url ?: '#' }}" class="block">
        {{ $slot }}
    </a>
</li>
