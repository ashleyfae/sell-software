<x-app>
    <x-slot name="header">Edit Product</x-slot>

    <form method="POST" action="{{ route('products.update', $product) }}">
        @csrf
        @method('PUT')

        @include('products._form', ['product' => $product])

        <p>
            <button type="submit">Update</button>
        </p>
    </form>
</x-app>
