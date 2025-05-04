<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Products\CreateNewProduct;
use App\Actions\Products\ListProducts;
use App\Actions\Products\UpdateProduct;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Product::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, ListProducts $listProducts) : View
    {
        return view('admin.products.list', [
            'products' => $listProducts->fromRequest($request),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() : View
    {
        return view('admin.products.create', [
            'product' => new Product(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request, CreateNewProduct $productCreator) : RedirectResponse
    {
        $product = $productCreator->createFromRequest($request);

        return redirect()->route('admin.products.show', $product);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product) : View
    {
        return view('admin.products.show', [
            'product' => $product,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product) : View
    {
        return view('admin.products.edit', ['product' => $product]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product, UpdateProduct $productUpdater) : RedirectResponse
    {
        $productUpdater->executeFromRequest($product, $request);

        $request->session()->flash('status', 'Product updated');

        return redirect()->route('admin.products.show', $product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
