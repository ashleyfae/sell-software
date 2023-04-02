<x-app>
    <x-slot name="header">{{ $product->name }}</x-slot>

    <a href="{{ route('products.edit', $product) }}" class="button">Edit Product</a>

    <h2>Details</h2>

    <ul class="aligned-list">
        <li>
            <span class="list-item--label">Name:</span>
            <span class="list-item--value">{{ $product->name }}</span>
        </li>

        <li>
            <span class="list-item--label">Git Repo:</span>
            <span class="list-item--value">{{ $product->git_repo }}</span>
        </li>

        <li>
            <span class="list-item--label">Stripe ID:</span>
            <span class="list-item--value">
                <a href="{{ $product->stripe_url }}" target="_blank">{{ $product->stripe_id }}</a>
            </span>
        </li>
    </ul>

    <h2>Pricing</h2>

    <a href="{{ route('products.prices.create', $product) }}" class="button">New Price</a>

    <table>
        <thead>
        <tr>
            <th>Name</th>
            <th>Price</th>
        </tr>
        </thead>
        <tbody>
        @forelse($product->prices as $price)
            <tr>
                <td>{{ $price->name }}</td>
                <td>{{ $price->price }}</td>
            </tr>
        @empty
        <tr>
            <td colspan="2">No prices yet.</td>
        </tr>
        @endforelse
        </tbody>
    </table>

</x-app>
