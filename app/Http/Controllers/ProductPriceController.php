<?php

namespace App\Http\Controllers;

use App\Actions\ProductPrices\CreateNewProductPrice;
use App\Http\Requests\StoreProductPriceRequest;
use App\Http\Requests\UpdateProductPriceRequest;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ProductPriceController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(ProductPrice::class, 'price');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Product $product) : View
    {
        return view('prices.create', [
            'product' => $product,
            'price' => $product->prices()->make(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductPriceRequest $request, Product $product, CreateNewProductPrice $priceCreator) : RedirectResponse
    {
        $price = $priceCreator->createFromRequest($request, $product);

        return redirect()->route('products.show', $product);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductPrice $productPrice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductPrice $productPrice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductPriceRequest $request, ProductPrice $productPrice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductPrice $productPrice)
    {
        //
    }
}
