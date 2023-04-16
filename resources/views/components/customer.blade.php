@extends('layouts.base')

@section('body')
    <div id="app" class="container px-6">
        <header id="header">
            @auth
                <ul>
                    <x-menus.item routeName="customer.downloads.list">
                        Downloads
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

        <section class="py-8 mb-4">
            @if($header ?? '')
                <header class="mb-6">
                    <h1 class="title {{ ($subtitle ?? null) ? 'mb-0' : '' }}">
                        {{ $header }}
                    </h1>

                    @if($subtitle ?? '')
                        <h2 class="subtitle">{{ $subtitle }}</h2>
                    @endif
                </header>
            @endif

            <main class="mx-auto">
                {{ $slot }}
            </main>
        </section>
    </div>
@endsection
