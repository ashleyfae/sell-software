<x-app>
    <x-slot name="header">Update Price</x-slot>

    <p>
        <a href="{{ route('products.show', $product) }}">
            &laquo; {{ $product->name }}
        </a>
    </p>

    <form method="POST" action="{{ route('products.prices.update', [$product, $price]) }}">
        @method('PATCH')
        @csrf
        @include('admin.products.prices._form', ['price' => $price])

        <p>
            <button type="submit">Update</button>
        </p>
    </form>

    <hr>

    <form method="POST" action="{{ route('products.prices.destroy', [$product, $price]) }}" class="delete">
        @method('DELETE')
        @csrf

        <button type="submit" class="danger">Delete</button>
    </form>
</x-app>
