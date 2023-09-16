<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopProduct;
use App\Models\StockMouvement;
use App\Models\TransferShopProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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
            $products = $products->where('name', 'LIKE', "%{$request->search}%")
                ->orWhere('code', 'LIKE', "%{$request->search}%");
        }
        $products = $products->paginate(10);
        $shops = Shop::get();
        $shopProducts = ShopProduct::get();

        if (request()->wantsJson()) {
            return ProductResource::collection($products);
        }

        return view('products.index')
            ->with('products', $products)
            ->with('shops', $shops)
            ->with('shopProducts', $shopProducts);
    }

    public function getProducts(Request $request, Shop $shop)
    {
        $products = new ShopProduct();
        $products = ShopProduct::with('product')
            ->where('shop_id', $shop->id);

        if ($request->search) {
            $products = $products->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->search}%")
                    ->orWhere('code', 'LIKE', "%{$request->search}%");
            });
        }

        $products = $products->latest()->paginate(10);

        return response()->json([
            'products' => $products,
        ], 200);
    }

    public function assignProducts(Product $product)
    {
        $shops = Shop::get();

        foreach ($shops as $shop) {
            $item = ShopProduct::with('product')->where('shop_id', $shop->id)
                ->where('product_id', $product->id)
                ->first();

            if ($item) {
                $shop->quantity = $item->quantity;
                $shop->product = $item->product;
            }
        }

        return view('products.assign')
            ->with('product', $product)
            ->with('shops', $shops);
    }

    public function saveAssignProducts(Request $request)
    {
        $productId = $request->product_id;
        $shops = $request->shops;
        $quantities = $request->quantity;
        $product = Product::find($productId);
        $isQuantityError = false;
        $totalQty = 0;

        foreach ($quantities as $qty) {
            $totalQty += $qty;
        }

        if ($totalQty > $product->quantity) {
            $validator = Validator::make([], []);
            $validator->errors()->add('quantity_exceded', 'This is the error message');

            throw new \Illuminate\Validation\ValidationException($validator);
        }

        foreach ($quantities as $key => $qty) {
            $transProd = TransferShopProduct::firstOrCreate([
                'product_id' => $productId,
                'shop_id' => $shops[$key],
            ]);

            $transProd->quantity = ($transProd->quantity ?? 0) + $qty;
            $transProd->save();

            $product->quantity -= $qty;
            $product->save();
        }

        return redirect()->route('assign.products', $product)
            ->with('success', 'Succes, articles envoyés avec succes.')
            ->with('product', $product)
            ->with('shops', Shop::get());
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
        // get quanitity
        $quantity = ($request->quantity_box * $request->items_in_box) + ($request->quantity_pce);

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'code' => $request->code,
            'buy_price' => $request->buy_price,
            'sell_price' => $request->sell_price,
            'quantity' => $quantity,
            'min_quantity' => $request->min_quantity,
            'items_in_box' => $request->items_in_box,
        ]);

        foreach (Shop::get() as $key => $shop) {
            $transProd = ShopProduct::firstOrCreate([
                'product_id' => $product->id,
                'shop_id' => $shop->id,
                'quantity' => 0,
            ]);
        }

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
        // get quanitity
        $product->quantity_box = 0;
        $product->quantity_pce = $product->quantity;

        if ($product->items_in_box && $product->items_in_box > 0) {
            $product->quantity_box = floor($product->quantity / $product->items_in_box);
            $product->quantity_pce = $product->quantity % $product->items_in_box;
        }
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

        // get quanitity
        $quantity = ($request->quantity_box * $request->items_in_box) + ($request->quantity_pce);

        if ($product->quantity != $quantity) {
            $hasQuantityChanged = true;
        }

        $product->name = $request->name;
        $product->description = $request->description;
        $product->code = $request->code;
        $product->buy_price = $request->buy_price;
        $product->sell_price = $request->sell_price;
        $product->items_in_box = $request->items_in_box;
        $product->quantity = $quantity;
        $product->min_quantity = $request->min_quantity;

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
