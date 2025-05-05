<x-app>
    <x-slot name="header">Products</x-slot>

    <div class="mb-4 text-right">
        <a href="{{ route('products.create') }}" class="button">New Product</a>
    </div>

    @if($products && $products->isNotEmpty())
        <div class="box with-padding">
            <table>
                <thead>
                <th>Name</th>
                <th>Created</th>
                <th>Updated</th>
                </thead>

                <tbody>
                @foreach($products as $product)
                    <tr>
                        <td>
                            <a href="{{ route('products.show', $product) }}">
                                {{ $product->name }}
                            </a>
                        </td>
                        <td>{{ $product->created_at->toFormattedDateString() }}</td>
                        <td>{{ $product->updated_at->toFormattedDateString() }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @else
        <x-elements.alert>
            <a href="{{ route('products.create') }}">Create a product.</a>
        </x-elements.alert>
    @endif
</x-app>
