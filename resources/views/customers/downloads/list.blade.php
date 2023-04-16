<x-customer>
    <x-slot name="header">Downloads</x-slot>

    @forelse($products as $product)
        <div class="download md:flex md:justify-between align-start gap-4">
            <div class="flex-grow-1 mb-3 md:mb-0">
                <h3>{{ $product->name }}</h3>

                <x-product-license-keys
                    :product="$product"
                />
            </div>

            @if($product->latestStableRelease)
                <div class="download--release-links mb-3 flex gap-2 flex-shrink-0">
                    <a
                        href="{{ route('release.download', $product->latestStableRelease) }}"
                        class="button"
                    >
                        Download v{{ $product->latestStableRelease->version }}
                    </a>

                    <a
                        href="{{ route('customer.products.releases', $product) }}"
                        class="button secondary"
                    >
                        All Releases
                    </a>
                </div>
            @endif
        </div>
    @empty
        <p>You have not purchased any products.</p>
    @endforelse

</x-customer>
