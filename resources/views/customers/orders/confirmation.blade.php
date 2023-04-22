<x-customer>
    <x-slot name="header">Thank you!</x-slot>

    <div class="md:flex justify-between">
        <div>
            <h2>Order List</h2>
            <div class="order-items-list">
                @foreach($order->orderItems as $orderItem)
                    <div class="order-items-list--item">
                        <div class="md:flex justify-between align-center gap-1">
                            <h3>{{ $orderItem->product_name }}</h3>
                            <div>{{ $orderItem->total }}</div>
                        </div>

                        @if($orderItem->license?->isActive() && $orderItem->license?->product->latestStableRelease)
                            <a href="{{ route('release.download', $orderItem->license->product->latestStableRelease) }}">
                                Download {{ $orderItem->license->product->latestStableRelease->file_name }}
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <div>
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
