<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopProduct;
use App\Models\StockMouvement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Shop $shop)
    {
        $products = new Product();
        if ($request->search) {
            $products = $products->where('name', 'LIKE', "%{$request->search}%");
        }
        $products = $products->latest()->paginate(10);
        $shops = Shop::get();
        $shopProducts = ShopProduct::get();

        if (request()->wantsJson()) {
            return ProductResource::collection($products);
        }

        // dd($shopProducts);
        return view('products.index')
            ->with('products', $products)
            ->with('shops', $shops)
            ->with('shopProducts', $shopProducts);
    }

    public function getProducts(Request $request, Shop $shop)
    {
        $products = new ShopProduct();
        // if ($request->search) {
        //     $products = $products->where('name', 'LIKE', "%{$request->search}%");
        // }
        $products = ShopProduct::with('product')
            ->where('shop_id', $shop->id);

        if ($request->search) {
            $products = $products->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->search}%");
            });
        }

        $products = $products->latest()->paginate(10);

        return response()->json([
            'products' => $products,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductStoreRequest $request)
    {
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'code' => $request->code,
            'buy_price' => $request->buy_price,
            'sell_price' => $request->sell_price,
            'quantity' => $request->quantity,
            'items_in_box' => $request->items_in_box,
        ]);

        //Log mouvement
        if ($product->quantity) {
            StockMouvement::create([
                'product_id' => $product->id,
                'type' => StockMouvement::INIT_STOCK,
                'quantity' => $product->quantity,
                'user_id' => Auth()->user()->id,
                'shop_id' => $request->shop_id,
            ]);
        }

        if (!$product) {
            return redirect()->back()->with('error', 'Desolé, une erreur c\'est produite.');
        }
        return redirect()->route('products.index')->with('success', 'Succes, votre produit a été créé.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        return view('products.edit')->with('product', $product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(ProductUpdateRequest $request, Product $product)
    {
        $hasQuantityChanged = false;
        if ($product->quantity != $request->quantity) {
            $hasQuantityChanged = true;
        }

        $product->name = $request->name;
        $product->description = $request->description;
        $product->code = $request->code;
        $product->buy_price = $request->buy_price;
        $product->sell_price = $request->sell_price;
        $product->items_in_box = $request->items_in_box;
        $product->quantity = $request->quantity;

        if (!$product->save()) {
            return redirect()->back()->with('error', 'Desolé, une erreur c\'est produite.');
        }

        if ($hasQuantityChanged) {
            //Log mouvement
            StockMouvement::create([
                'product_id' => $product->id,
                'type' => StockMouvement::MANUAL_EDIT,
                'quantity' => $product->quantity,
                'user_id' => Auth()->user()->id,
                'shop_id' => $request->shop_id,
            ]);
        }
        return redirect()->route('products.index')->with('success', 'Succes, l\'article a été mise a jour.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::delete($product->image);
        }
        $product->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
