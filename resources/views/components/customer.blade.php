@extends('layouts.base')

@section('body')
    <div id="app">
        <header id="header" class="container">
            @auth
                <ul>
                    <x-menus.item routeName="products.index">
                        Orders
                    </x-menus.item>
                </ul>
            @endauth
        </header>

        <section class="px-6 py-8 mb-4">
            @if($header ?? '')
                <header class="container mb-6">
                    <h1 class="title">
                        {{ $header }}
                    </h1>
                </header>
            @endif

            <main class="container mx-auto">
                {{ $slot }}
            </main>
        </section>
    </div>
@endsection
