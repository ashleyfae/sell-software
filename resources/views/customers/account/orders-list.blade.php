@extends('customers.account.layout')

@section('subtitle') Orders @endsection

@section('content')
    <table>
        <thead>
        <tr>
            <th>Order Number</th>
            <th>Total</th>
            <th>Date</th>
            <th>Status</th>
            <th>Invoice</th>
        </tr>
        </thead>

        <tbody>
        @if($orders && $orders->isNotEmpty())
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->total }}</td>
                    <td>{{ $order->created_at->toDateString() }}</td>
                    <td>
                        <x-elements.order-status :status="$order->status"/>
                    </td>
                    <td></td>
                </tr>
            @endforeach
        @else
        <tr>
            <td colspan="5">You have not placed any orders yet.</td>
        </tr>
        @endif
        </tbody>
    </table>

    @if($orders && $orders->isNotEmpty())
        {{ $orders->links() }}
    @endif
@endsection
