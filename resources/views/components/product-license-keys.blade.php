<div
    class="product-license-keys"
>
    <button
        type="button"
        class="plain download--toggle-licenses"
        data-route="{{ route('customer.products.licenses', $product) }}"
    >Show license key(s)</button>

    <div class="download--licenses hidden"></div>

    <template>
        <div class="download--license flex align-center gap-1">
            <input
                type="text"
                value=""
                readonly
            >

            <a href="#">Manage</a>
        </div>
    </template>

</div>
