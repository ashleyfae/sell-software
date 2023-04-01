<?php

namespace App\Http\Controllers;

use App\Actions\Stores\CreateNewStore;
use App\Http\Requests\Stores\StoreStoreRequest;
use App\Models\Store;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->authorizeResource(Store::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('store.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStoreRequest $request, CreateNewStore $storeCreator) : RedirectResponse
    {
        $store = $storeCreator->createFromRequest($request);

        return redirect(route('stores.edit', $store));
    }

    /**
     * Display the specified resource.
     */
    public function show(Store $store)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Store $store)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Store $store)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Store $store)
    {
        //
    }
}
