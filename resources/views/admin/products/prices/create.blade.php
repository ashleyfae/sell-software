<x-app>
    <x-slot name="header">Create Price</x-slot>

    <p>
        <a href="{{ route('products.show', $product) }}">
            &laquo; {{ $product->name }}
        </a>
    </p>

    <form method="POST" action="{{ route('products.prices.store', $product) }}">
        @csrf
        @include('admin.products.prices._form', ['price' => $price])

        <p>
            <button type="submit">Create</button>
        </p>
    </form>
</x-app>
