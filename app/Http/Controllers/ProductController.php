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
use App\Models\UpdatedStock;
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

            // check if is code
            if ((int)$request->search == $request->search) {
                $products = $products->where('code', '=', $request->search);
            } else {
                $products = $products->where('name', 'LIKE', "%{$request->search}%");
            }
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
                // check if is code
                if ((int)$request->search == $request->search) {
                    $q->where('code', '=', $request->search);
                } else {
                    $q->where('name', 'LIKE', "%{$request->search}%");
                }
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

        $qtyBeforeStore = $product->quantity;

        foreach ($quantities as $qty) {
            $totalQty += $qty;
        }

        if ($totalQty > $product->quantity) {
            $validator = Validator::make([], []);
            $validator->errors()->add('quantity_exceded', 'This is the error message');

            throw new \Illuminate\Validation\ValidationException($validator);
        }

        foreach ($quantities as $key => $qty) {
            if ($qty > 0) {
                $shopProduct = ShopProduct::firstOrCreate([
                    'product_id' => $productId,
                    'shop_id' => $shops[$key],
                ]);

                $qtyBeforeShop = $shopProduct->quantity;
                $qtyAfterShop = null;

                $shopProduct->quantity = ($shopProduct->quantity ?? 0) + $qty;
                $shopProduct->save();

                $qtyAfterShop = $shopProduct->quantity;

                $product->quantity -= $qty;
                $product->save();

                $qtyAfterStore = $product->quantity;

                //Log mouvement shop
                UpdatedStock::create([
                    'product_id' => $productId,
                    'quantity' => $qty,
                    'sent_by' => $request->user_id,
                    'shop_id' => $shops[$key],
                ]);

                //Log mouvement shop
                StockMouvement::create([
                    'product_id' => $productId,
                    'type' => StockMouvement::SHOP_INCREASE,
                    'quantity' => $qty,
                    'user_id' => $request->user_id,
                    'shop_id' => $shops[$key],
                    'quantity_before' => $qtyBeforeShop,
                    'quantity_after' => $qtyAfterShop,
                ]);

                //Log mouvement store
                StockMouvement::create([
                    'product_id' => $productId,
                    'type' => StockMouvement::STORE_DECREASED,
                    'quantity' => $qty,
                    'user_id' => $request->user_id,
                    'shop_id' => $shops[$key],
                    'quantity_before' => $qtyBeforeStore,
                    'quantity_after' => $qtyAfterStore,
                ]);
            }
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

        //prices
        $lushi = Shop::where('name', Shop::LUBUMBASHI)->first();
        $kolwezi = Shop::where('name', Shop::KOLWEZI)->first();
        $kilwa = Shop::where('name', Shop::KILWA)->first();

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'code' => $request->code,
            'buy_price' => $request->buy_price,
            // 'sell_price' => $request->sell_price,
            'quantity' => $quantity,
            'min_quantity' => $request->min_quantity,
            'items_in_box' => $request->items_in_box,
        ]);

        $qtyBefore = 0;
        $qtyAfter = $product->quantity;

        foreach (Shop::get() as $key => $shop) {
            $shopProd = ShopProduct::firstOrCreate([
                'product_id' => $product->id,
                'shop_id' => $shop->id,
                'quantity' => 0,
                'sell_price' => $request->sell_price,
            ]);

            if ($shopProd->shop_id == $lushi->id) {
                $shopProd->sell_price = $request->sell_price_lushi;

            } else if ($shopProd->shop_id == $kolwezi->id) {
                $shopProd->sell_price = $request->sell_price_kolwezi;

            } else if ($shopProd->shop_id == $kilwa->id) {
                $shopProd->sell_price = $request->sell_price_kilwa;

            }

            $shopProd->save();
        }

        //Log mouvement
        if ($product->quantity) {
            StockMouvement::create([
                'product_id' => $product->id,
                'type' => StockMouvement::INIT_STOCK,
                'quantity' => $product->quantity,
                'user_id' => Auth()->user()->id,
                'shop_id' => $request->shop_id,
                'quantity_before' => $qtyBefore,
                'quantity_after' => $qtyAfter,
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

        //prices
        $lushi = Shop::where('name', Shop::LUBUMBASHI)->first();
        $kolwezi = Shop::where('name', Shop::KOLWEZI)->first();
        $kilwa = Shop::where('name', Shop::KILWA)->first();

        foreach (Shop::get() as $key => $shop) {
            $lushiProd = ShopProduct::where('product_id', $product->id)
                ->where('shop_id', $lushi->id)
                ->first();
            $kolweziProd = ShopProduct::where('product_id', $product->id)
                ->where('shop_id', $kolwezi->id)
                ->first();
            $kilwaProd = ShopProduct::where('product_id', $product->id)
                ->where('shop_id', $kilwa->id)
                ->first();

            $product->sell_price_lushi = $lushiProd ? $lushiProd->sell_price : 0;
            $product->sell_price_kolwezi = $kolweziProd ? $kolweziProd->sell_price : 0;
            $product->sell_price_kilwa = $kilwaProd ? $kilwaProd->sell_price : 0;
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
        $qtyBefore = $product->quantity;
        $qtyAfter = null;

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

        //prices
        $lushi = Shop::where('name', Shop::LUBUMBASHI)->first();
        $kolwezi = Shop::where('name', Shop::KOLWEZI)->first();
        $kilwa = Shop::where('name', Shop::KILWA)->first();

        foreach (Shop::get() as $key => $shop) {
            $shopProd = ShopProduct::firstOrCreate([
                'product_id' => $product->id,
                'shop_id' => $shop->id,
            ]);

            if ($shopProd->shop_id == $lushi->id) {
                $shopProd->sell_price = $request->sell_price_lushi;

            } else if ($shopProd->shop_id == $kolwezi->id) {
                $shopProd->sell_price = $request->sell_price_kolwezi;

            } else if ($shopProd->shop_id == $kilwa->id) {
                $shopProd->sell_price = $request->sell_price_kilwa;
            }

            $shopProd->save();
        }

        if (!$product->save()) {
            return redirect()->back()->with('error', 'Desolé, une erreur c\'est produite.');
        }

        $qtyAfter = $product->quantity;

        if ($hasQuantityChanged) {
            //Log mouvement
            StockMouvement::create([
                'product_id' => $product->id,
                'type' => StockMouvement::MANUAL_EDIT,
                'quantity' => $product->quantity,
                'user_id' => Auth()->user()->id,
                'shop_id' => $request->shop_id,
                'quantity_before' => $qtyBefore,
                'quantity_after' => $qtyAfter,
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

    public function shopsStockMovements(Request $request) {
        $user = Auth()->user();
        $movements = UpdatedStock::with(['product', 'shop']);
        $shops = Shop::get();

        $shopId = '0';
        if (!$user->is_admin) {
            $shopId = Shop::where('name', $user->shop_name)->first()->id;
            $movements = $movements->where('shop_id', $shopId);
        }

        if( $user->is_admin && $request->shop) {
            $movements = $movements->where('shop_id', $request->shop);
            $shopId = $request->shop;
        }

        if($request->start_date) {
            $movements = $movements->where('created_at', '>=', $request->start_date);
        }

        if($request->end_date) {
            $movements = $movements->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }

        if($request->code) {
            $code = $request->code;

            $movements = $movements->whereHas('product', function($q) use($code) {
                $q->where('code', '=', $code);
            });
        }

        $movements = $movements->with(['product', 'shop'])->latest()->paginate(10);

        return view('products.movements', compact('movements',
            'user',
            'shopId',
            'shops'
        ));
    }

    public function stockMouvement(Request $request) {
        $user = Auth()->user();
        $movements = StockMouvement::with(['product', 'shop']);
        $shops = Shop::get();

        $shopId = '0';
        if (!$user->is_admin) {
            $mouvementsFilter = [
                StockMouvement::CREATE_BILL,
                StockMouvement::CANCEL_BILL,
                StockMouvement::SHOP_INCREASE,
                StockMouvement::SHOP_EDITED,
                StockMouvement::MANUAL_EDIT,
            ];

            $shopId = Shop::where('name', $user->shop_name)->first()->id;
            $movements = $movements->where('shop_id', $shopId)
                ->whereIn('type', $mouvementsFilter);
        }

        if( $user->is_admin && $request->shop) {
            $movements = $movements->where('shop_id', $request->shop);
            $shopId = $request->shop;
        }

        if($request->start_date) {
            $movements = $movements->where('created_at', '>=', $request->start_date);
        }

        if($request->end_date) {
            $movements = $movements->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }

        if($request->code) {
            $code = $request->code;

            $movements = $movements->whereHas('product', function($q) use($code) {
                $q->where('code', '=', $code);
            });
        }

        $movements = $movements->with(['product', 'shop'])->latest()->paginate(20);

        return view('products.history', compact('movements',
            'user',
            'shopId',
            'shops'
        ));
    }
}
