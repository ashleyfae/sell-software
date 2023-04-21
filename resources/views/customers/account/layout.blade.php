<x-customer>
    <x-slot name="header">Account Settings</x-slot>

    <x-layouts.box-with-side-nav>
        <x-slot name="nav">
            <x-menus.side-item
                routeName="customer.account.contact.show"
            >Contact Information</x-menus.side-item>

            <x-menus.side-item
                routeName="customer.account.orders.list"
            >Orders</x-menus.side-item>
        </x-slot>

        <h2 class="subtitle">
            @yield('subtitle')
        </h2>

        @yield('content')
    </x-layouts.box-with-side-nav>
</x-customer>
