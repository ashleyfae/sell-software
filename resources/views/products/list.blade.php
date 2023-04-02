<x-app>
    <x-slot name="header">Products</x-slot>

    @if($products && $products->isNotEmpty())
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
    @else
        <x-elements.alert>
            <a href="{{ route('products.create') }}">Create a product.</a>
        </x-elements.alert>
    @endif
</x-app>
