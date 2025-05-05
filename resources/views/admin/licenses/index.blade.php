<x-app>
    <x-slot name="header">Licenses</x-slot>

    <form method="GET" action="{{ route('admin.licenses.index') }}" class="flex">
        <div>
            <input
                type="text"
                id="license_key"
                name="license_key"
                value="{{ old('license_key', request()->input('license_key')) }}"
                placeholder="License key"
            >
            <x-forms.input-error name="license_key" />
        </div>

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

    @if($licenses && $licenses->isNotEmpty())
        <table>
            <thead>
                <th>Key</th>
                <th>Product</th>
                <th>Status</th>
                <th>Customer</th>
            </thead>

            <tbody>
            @foreach($licenses as $license)
                <?php /** @var \App\Models\License $license */ ?>
                <tr>
                    <td>
                        <a href="{{ route('customer.licenses.show', $license) }}">
                            {{ $license->license_key }}
                        </a>
                    </td>
                    <td>
                        {{ $license->product->name }}
                    </td>
                    <td>
                        <x-elements.license-key-status :status="$license->status" />
                    </td>
                    <td>
                        {{ $license->user->email }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $licenses->links() }}
    @else
        <x-elements.alert>
            No licenses found.
        </x-elements.alert>
    @endif
</x-app>
