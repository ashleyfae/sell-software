<ul>
    <x-menus.item routeName="customer.downloads.list">
        Downloads
    </x-menus.item>
    <x-menus.item routeName="customer.account.contact.show">
        Account
    </x-menus.item>
    @if(auth()->user()->isAdmin())
        <x-menus.item routeName="products.index">
            Admin
        </x-menus.item>
    @endif
</ul>
