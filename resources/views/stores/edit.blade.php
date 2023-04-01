<x-app>
    <x-slot name="header">Edit Store</x-slot>

    <form method="POST" action="{{ route('stores.update', $store) }}">
        @csrf
        <p>
            <label for="name">Name:</label> <br>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $store->name) }}"
                required
            >
        </p>
        <x-forms.input-error name="name" />

        <p>
            <button type="submit">Update</button>
        </p>
    </form>

    @if($store->stripe_account_id)
        <p>
            Connected to Stripe
        </p>
    @else
        <p>
            <a href="{{ route('stores.connect', $store) }}">Connect to Stripe</a>
        </p>
    @endif
</x-app>
