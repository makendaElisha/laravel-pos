<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopProduct;
use App\Models\StockMouvement;
use Illuminate\Http\Request;

class ProductQuery extends Controller
{
    public function getShopProductQuantity(Shop $shop, Product $product)
    {
        dd($shop, $product);

    }
    public function setShopProductQuantity(Request $request)
    {
        $shopProd = ShopProduct::find($request->shop_prod_id);
        $shopProd->quantity = $shopProd->quantity + $request->quantity;
        $shopProd->save();

        //Log mouvement
        StockMouvement::create([
            'product_id' => $request->product_id,
            'type' => StockMouvement::SHOP_INCREASE,
            'quantity' => $request->quantity,
            'user_id' => $request->user_id,
            'shop_id' => $request->shop_id,
        ]);

        return response()->json([
            'product' => $shopProd,
        ], 200);
    }
    public function createProductInShop(Shop $shop, Product $product)
    {
        $shopProd = ShopProduct::firstOrCreate([
            'shop_id' => $shop->id,
            'product_id' => $product->id,
            'quantity' => 0,
        ]);

        return response()->json([
            'product' => $shopProd,
        ], 200);
    }
}
