@extends('layouts.base')

@section('body')
    <div class="container">
        <h1>Create Store</h1>

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
            @error('name')
            <span class="colour--danger">{{ $message }}</span>
            @enderror

            <p>
                <button type="submit">Create</button>
            </p>
        </form>
    </div>
@endsection
