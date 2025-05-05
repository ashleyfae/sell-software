<?php /** @var \App\Models\Order $order */ ?>
@extends('layouts.base')

@section('title') Order {{ $order->display_id }} @endsection

@section('body')
    <div id="customer-order" class="container">
        <div id="order-details">
            <h1>Order: {{ $order->display_id }}</h1>

            <div id="customer-details" class="box">
                <div>
                    <strong>Nose Graze Limited</strong>
                </div>
                <div>
                    <strong>Billed to:</strong> <br>
                    @if($order->user->name) {{ $order->user->name }} <br> @endif
                    {{ $order->user->email }}
                </div>
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
                    <span class="label">Paid Via:</span>
                    <span class="value">{{ $order->gateway->getCustomerLabel() }}</span>
                </div>
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
    </div>
@endsection
