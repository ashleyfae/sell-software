<x-app>
    <x-slot name="header">Create Store</x-slot>

    <form method="POST" action="{{ route('stores.store') }}">
        @csrf
        <p>
            <label for="name">Name:</label> <br>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                autofocus
                required
            >
        </p>
        <x-forms.input-error name="name" />

        <p>
            <button type="submit">Create</button>
        </p>
    </form>
</x-app>
