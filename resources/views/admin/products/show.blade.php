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
            <th>Stripe</th>
            <th>Purchase URL</th>
        </tr>
        </thead>
        <tbody>
        @forelse($product->prices as $price)
            <?php /** @var \App\Models\ProductPrice $price */ ?>
            <tr>
                <td>
                    <a href="{{ route('products.prices.edit', [$product, $price]) }}">
                        {{ $price->name }}
                    </a>
                </td>
                <td>
                    <a href="{{ $price->stripeUrl }}" target="_blank">
                        {{ $price->stripe_id }}
                    </a>
                </td>
                <td>
                    <label for="price-{{ $price->id }}-purchase-link" class="sr-only">Price purchase URL</label>
                    <input
                        type="text"
                        id="price-{{ $price->id }}-purchase-link"
                        value="{{ route('buy', $price) }}"
                        readonly
                    >
                </td>
            </tr>
        @empty
        <tr>
            <td colspan="2">No prices yet.</td>
        </tr>
        @endforelse
        </tbody>
    </table>

</x-app>
