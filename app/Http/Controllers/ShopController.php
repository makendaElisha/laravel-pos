<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopProduct;
use App\Models\StockMouvement;
use App\Models\TransferShopProduct;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Shop $shop)
    {
        $search = $request->search;
        $shopProducts = ShopProduct::with('product')
            ->where('shop_id', $shop->id)
            ->whereHas('product', function ($query) {
                // Add a condition to check if the product exists
                $query->whereNotNull('id');
            });

        if ($search) {
            $shopProducts->whereHas('product', function($q) use($search) {
                // check if is code
                if ((int)$search == $search) {
                    $q->where('code', '=', $search);
                } else {
                    $q->where('name', 'LIKE', "%{$search}%");
                }
            });
        }

        $shopProducts = $shopProducts->paginate(10);
        $shops = Shop::get();

        // foreach ($shopProducts as $key => $shopProd) {
        //     $trans = TransferShopProduct::where('shop_id', $shop->id)
        //         ->where('product_id', $shopProd->product_id)
        //         ->first();
        //     if ($trans) {
        //         $shopProd->transfer_quantity = $trans->quantity;
        //         $shopProd->transfer_id = $trans->id;
        //     }

        // }

        return view('shopProducts.index')->with([
            'products' => $shopProducts,
            'shop' => $shop,
            'shops' => $shops,
            'user' => Auth()->user(),
            'userId' => Auth()->user()->id,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function updateStock(Request $request, Shop $shop, Product $product)
    {
        $shopProd = ShopProduct::firstOrCreate(['shop_id', $shop->id, 'product_id', $product->id]);

        $qtyBefore = $shopProd->quantity;
        $qtyAfter = null;

        $shopProd->quantity += $request->quantity;
        $shopProd->save();

        $qtyAfter = $shopProd->quantity;

        //Log mouvement
        StockMouvement::create([
            'product_id' => $product->id,
            'type' => StockMouvement::STORE_INCREASE,
            'quantity' => $product->quantity,
            'user_id' => Auth()->user()->id,
            'shop_id' => $request->shop_id,
            'quantity_before' => $qtyBefore,
            'quantity_after' => $qtyAfter,
        ]);

        return response([
            'product' => $shopProd,
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Shop $shop)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shop $shop)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shop $shop)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shop $shop)
    {
        //
    }
}
