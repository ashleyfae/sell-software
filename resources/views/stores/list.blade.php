<x-app>
    <x-slot name="header">Stores</x-slot>

    <a href="{{ route('stores.create') }}" class="button">New Store</a>

    @if($stores && $stores->isNotEmpty())
    <ul>
        @foreach($stores as $store)
            <li>{{ $store->name }}</li>
        @endforeach
    </ul>
    @endif
</x-app>
