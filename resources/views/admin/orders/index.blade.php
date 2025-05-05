<x-app>
    <x-slot name="header">Orders</x-slot>

    <form method="GET" action="{{ route('admin.orders.index') }}" class="flex">
        <div>
            <input
                type="text"
                id="customer_email"
                name="customer_email"
                value="{{ old('customer_email', request()->input('customer_email')) }}"
                placeholder="Customer email"
            >
            <x-forms.input-error name="customer_email" />
        </div>

        <div>
            <button type="submit">Search</button>
        </div>
    </form>

    @if($orders && $orders->isNotEmpty())
        <table>
            <thead>
            <th>ID</th>
            <th>Product(s)</th>
            <th>Status</th>
            <th>Customer</th>
            <th>Created</th>
            </thead>

            <tbody>
            @foreach($orders as $order)
                <?php /** @var \App\Models\Order $order */ ?>
                <tr>
                    <td>
                        <a href="{{ route('admin.orders.show', $order) }}">
                            {{ $order->display_id }}
                        </a>
                    </td>
                    <td>
                        {{ implode(', ', $order->orderItems->pluck('product_name')->toArray()) }}
                    </td>
                    <td>
                        {{ $order->status->displayName() }}
                    </td>
                    <td>
                        {{ $order->user->email }}
                    </td>
                    <td>{{ $order->created_at }} UTC</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $orders->links() }}
    @else
        <x-elements.alert>
            No orders found.
        </x-elements.alert>
    @endif
</x-app>
