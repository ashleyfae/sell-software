@extends('layouts.base')

@section('body')
    <div id="app">
        <header id="header" class="container">
            @auth
                <ul>
                    <x-menus.item routeName="products.index">
                        Products
                    </x-menus.item>

                    <x-menus.item routeName="admin.licenses.index">
                        Licenses
                    </x-menus.item>

                    <x-menus.item routeName="admin.orders.index">
                        Orders
                    </x-menus.item>

                    <x-menus.item routeName="customer.downloads.list">
                        &times; Exit
                    </x-menus.item>
                </ul>
            @endauth
        </header>

        @if($breadcrumbs ?? null)
            <nav class="breadcrumbs pt-4">
                <ul>
                    {{ $breadcrumbs }}
                </ul>
            </nav>
        @endif

        <section class="px-6 py-8 mb-4">
            @if($header ?? '')
                <header class="container mb-6">
                    <h1 class="title">
                        {{ $header }}
                    </h1>
                </header>
            @endif

            <main class="container mx-auto">
                @if(session()->get('status'))
                    <x-elements.alert type="success">{{ session()->get('status') }}</x-elements.alert>
                @endif

                {{ $slot }}
            </main>
        </section>
    </div>
@endsection

@section('footer')
    <script src="{{ asset('js/admin.js') }}" defer></script>
@endsection
