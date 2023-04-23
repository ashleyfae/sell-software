<x-customer>
    <x-slot name="header">
        @if($order->isRenewal())
            Renewal successful!
        @else
            Thank you!
        @endif
    </x-slot>

    @if($order->isRenewal())
        <p>
            @if(count($order->orderItems) === 1)
                Your license key has been successfully renewed.
            @else
                Your license keys have been successfully renewed.
            @endif
            You do not need to re-install the plugins; the new expiration date will be acknowledged within a few hours.
        </p>
    @endif

    <div class="md:flex justify-between gap-5">
        <div class="flex-grow-1">
            <h2>Order List</h2>
            <div class="order-items-list">
                @foreach($order->orderItems as $orderItem)
                    <div class="order-items-list--item box with-padding mb-5">
                        <div class="md:flex justify-between align-center gap-1">
                            <h3 class="mt-0 mb-1">{{ $orderItem->product_name }}</h3>
                            <div>{{ $orderItem->total }}</div>
                        </div>

                        @if($orderItem->license)
                            @if($orderItem->license->isActive() && $orderItem->license?->product->latestStableRelease)
                                <a href="{{ route('release.download', $orderItem->license->product->latestStableRelease) }}">
                                    Download {{ $orderItem->license->product->latestStableRelease->file_name }}
                                </a>
                            @endif

                            <div class="md:flex mt-4 gap-2">
                                <h4 class="my-0">License Key:</h4>

                                <x-elements.tag>
                                    Expires: {{ $orderItem->license->expires_at->toFormattedDateString() }}
                                </x-elements.tag>
                            </div>

                            <x-elements.license-key-input :license="$orderItem->license" />
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex-shrink-0">
            <h2>Order Summary</h2>

            <ul>
                <li class="md:flex justify-between">
                    <span>Subtotal:</span>
                    <span>{{ $order->subtotal }}</span>
                </li>
                @if($order->discount->amount > 0)
                    <li class="md:flex justify-between">
                        <span>Discount:</span>
                        <span>{{ $order->discount }}</span>
                    </li>
                @endif
                @if($order->tax->amount > 0)
                    <li class="md:flex justify-between">
                        <span>VAT:</span>
                        <span>{{ $order->tax }}</span>
                    </li>
                @endif
                <li class="md:flex justify-between">
                    <span>Total:</span>
                    <span>{{ $order->total }}</span>
                </li>
            </ul>
        </div>
    </div>
</x-customer>
