<?php /** @var \App\Models\Order $order */ ?>
<x-app>
    <x-slot name="header">Order #{{ $order->id }}</x-slot>

    <div id="order-details">
        <div id="customer-details" class="box">
            @if($order->user->name)
                <div>
                    <span class="label">Customer Name</span>
                    <span class="value">{{ $order->user->name }}</span>
                </div>
            @endif
            <div>
                <span class="label">Customer Email</span>
                <span class="value">{{ $order->user->email }}</span>
            </div>
            @if($order->ip)
                <div>
                    <span class="label">Customer IP</span>
                    <span class="value">{{ $order->ip }}</span>
                </div>
            @endif
        </div>

        <div id="order-summary" class="box">
            <div>
                <span class="label">Order Total:</span>
                <span class="value">{{ $order->total }}</span>
            </div>
            <div>
                <span class="label">Date Paid:</span>
                <span class="value">{{ $order->completed_at->toDayDateTimeString() }}</span>
            </div>
            <div>
                <span class="label">Gateway:</span>
                <span class="value">{{ $order->gateway->name }}</span>
            </div>
            @if($order->stripe_payment_intent_id)
                <div>
                    <span class="label">Stripe PI:</span>
                    <span class="value">
                        <a href="{{ \App\Helpers\StripeHelper::dashboardUrl('payments/'.urlencode($order->stripe_payment_intent_id)) }}">
                            {{ $order->stripe_payment_intent_id }}
                        </a>
                    </span>
                </div>
            @endif
        </div>

        <div id="order-items" class="box">
            <table>
                <tbody>
                @foreach($order->orderItems as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->subtotal }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <td>Discount</td>
                    <td>{{ $order->discount }}</td>
                </tr>
                <tr>
                    <td>Tax</td>
                    <td>{{ $order->tax }}</td>
                </tr>
                <tr id="order-item-total">
                    <td>Total</td>
                    <td>{{ $order->total }}</td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</x-app>
