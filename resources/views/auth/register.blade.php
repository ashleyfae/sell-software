@extends('layouts.base')

@section('body')
    <div id="login" class="container slim">
        <h1>Register</h1>

        <p>
            Already registered? <a href="{{ route('login') }}">Log in.</a>
        </p>

        @if(session('error'))
            <x-elements.alert type="error">
                {{ session('error') }}
            </x-elements.alert>
        @endif

        <form method="POST" action="{{ route('register') }}">
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
                <label for="email">Email:</label> <br>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                >
            </p>
            <x-forms.input-error name="email" />

            <p>
                <label for="password">Password:</label> <br>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                >
            </p>
            <x-forms.input-error name="password" />

            <p>
                <label for="password_confirmation">Confirm Password:</label> <br>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    required
                >
            </p>

            <p>
                <button type="submit">Register</button>
            </p>
        </form>
    </div>
@endsection
