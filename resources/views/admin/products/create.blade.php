<x-app>
    <x-slot name="header">Create Product</x-slot>

    <form method="POST" action="{{ route('products.store') }}">
        @csrf
        @include('admin.products._form', ['product' => $product])

        <p>
            <button type="submit">Create</button>
        </p>
    </form>
</x-app>
