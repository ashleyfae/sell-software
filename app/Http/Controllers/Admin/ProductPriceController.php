<?php

namespace App\Http\Controllers\Admin;

use App\Actions\ProductPrices\CreateNewProductPrice;
use App\Actions\ProductPrices\UpdateProductPrice;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductPriceRequest;
use App\Http\Requests\Admin\UpdateProductPriceRequest;
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
        return view('admin.products.prices.create', [
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
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product, ProductPrice $price): View
    {
        return view('admin.products.prices.edit', [
            'product' => $product,
            'price'   => $price,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateProductPriceRequest $request,
        Product $product,
        ProductPrice $price,
        UpdateProductPrice $updateProductPrice
    ): RedirectResponse
    {
        $updateProductPrice->createFromRequest($request, $price);

        if ($request->wantsJson()) {
            return response()->json($price->toArray());
        } else {
            $request->session()->flash('status', 'Price updated successfully.');

            return redirect()->route('products.show', $price->product);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Request $request,
        Product $product,
        ProductPrice $price
    ): RedirectResponse|JsonResponse {
        $price->delete();

        if ($request->wantsJson()) {
            return response()->json(null);
        } else {
            $request->session()->flash('status', 'Price deleted successfully.');

            return redirect()->route('products.show', $product);
        }
    }
}
