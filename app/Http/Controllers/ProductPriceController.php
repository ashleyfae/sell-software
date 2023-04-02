<?php

namespace App\Http\Controllers;

use App\Actions\ProductPrices\CreateNewProductPrice;
use App\Http\Requests\StoreProductPriceRequest;
use App\Http\Requests\UpdateProductPriceRequest;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductPriceController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(ProductPrice::class, 'price');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Product $product): View
    {
        return view('prices.create', [
            'product' => $product,
            'price'   => $product->prices()->make(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        StoreProductPriceRequest $request,
        Product $product,
        CreateNewProductPrice $priceCreator
    ): RedirectResponse|JsonResponse {
        $price = $priceCreator->createFromRequest($request, $product);

        if ($request->wantsJson()) {
            return response()->json($price->toArray());
        } else {
            $request->session()->flash('status', 'Price created successfully.');

            return redirect()->route('products.show', $product);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductPrice $productPrice): View
    {
        // @TODO do I want this?
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product, ProductPrice $productPrice): View
    {
        return view('prices.edit', [
            'product' => $product,
            'price'   => $productPrice,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductPriceRequest $request, ProductPrice $productPrice): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Request $request,
        Product $product,
        ProductPrice $productPrice
    ): RedirectResponse|JsonResponse {
        $productPrice->delete();

        if ($request->wantsJson()) {
            return response()->json(null);
        } else {
            $request->session()->flash('status', 'Price deleted successfully.');

            return redirect()->route('products.show', $product);
        }
    }
}
