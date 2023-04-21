@extends('customers.account.layout')

@section('subtitle') Contact Information @endsection

@section('content')
    <form method="POST" action="{{ route('customer.account.contact.update') }}">
        @csrf

        <p>
            <label for="name">Full Name:</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $user->name) }}"
                required
            >
        </p>
        <x-forms.input-error name="name" />

        <p>
            <label for="email">Email:</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email', $user->email) }}"
                required
            >
        </p>
        <x-forms.input-error name="email" />

        <div class="md:text-right mb-0 mt-5">
            <button type="submit">Update</button>
        </div>
    </form>
@endsection
