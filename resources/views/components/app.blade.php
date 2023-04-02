@extends('layouts.base')

@section('body')
    <div id="app">
        <header id="header" class="container">
            @auth
                <ul>
                    @if(! empty($currentStore))
                        <li>
                            @if($stores->count() > 1)
                                <select>
                                    @foreach($stores as $store)
                                        <option
                                            @selected($store->is($currentStore))
                                        >
                                            {{ $store->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                {{ $currentStore->name }}
                            @endif
                        </li>
                    @else
                        <li>
                            <a href="{{ route('stores.create') }}">Create Store</a>
                        </li>
                    @endif
                </ul>

                <ul>
                    <x-menus.item routeName="products.index">
                        Products
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
