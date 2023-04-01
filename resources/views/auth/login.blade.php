@extends('layouts.base')

@section('body')
    <div id="login" class="container">
        <h1>Log in</h1>

        <p>
            Don't have an account? <a href="{{ route('register') }}">Register</a>
        </p>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <p>
                <label for="email">Email:</label> <br>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    autofocus
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
                <input
                    type="checkbox"
                    id="remember"
                    name="remember"
                    checked
                >
                <label for="remember">Remember me</label>
            </p>

            <p>
                <button type="submit">Login</button>
            </p>
        </form>
    </div>
@endsection
