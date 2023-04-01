@extends('layouts.base')

@section('body')
    <div id="login" class="container">
        <h1>Register</h1>

        <p>
            Already registered? <a href="{{ route('login') }}">Log in.</a>
        </p>

        @if(session('error'))
            {{ session('error') }}
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
            @error('name')
            <span class="colour--danger">{{ $message }}</span>
            @enderror

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
            @error('email')
            <span class="colour--danger">{{ $message }}</span>
            @enderror

            <p>
                <label for="password">Password:</label> <br>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                >
            </p>
            @error('password')
            <span class="colour--danger">{{ $message }}</span>
            @enderror

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
