<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopProduct;
use App\Models\StockMouvement;
use App\Models\TransferShopProduct;
use App\Models\UpdatedStock;
use Illuminate\Http\Request;

class ProductQuery extends Controller
{
    public function getShopProductQuantity($shopId, $productId)
    {
       return response()->json([
        'product' => ShopProduct::where('shop_id', $shopId)->where('product_id', $productId)->first(),
       ], 200);

    }

    public function setShopProductQuantity(Request $request)
    {
        $success = false;
        $qtyBox = $request->quantity_box;
        $qtyPce = $request->quantity_pce;
        $product = Product::find($request->product_id);
        $quantity = $qtyPce;

        if ($qtyBox && $product->items_in_box > 0) {
            $quantity = $qtyPce + floor($qtyBox * $product->items_in_box);
        }

        $shopProd = ShopProduct::find($request->shop_prod_id);
        $shopProd->quantity = $quantity;
        $shopProd->save();

        $success = true;

        //Log mouvement
        StockMouvement::create([
            'product_id' => $request->product_id,
            'type' => StockMouvement::SHOP_EDITED,
            'quantity' => $quantity,
            'user_id' => $request->user_id,
            'shop_id' => $request->shop_id,
        ]);

        //Log notification shop
        UpdatedStock::create([
            'product_id' => $request->product_id,
            'quantity' => $quantity,
            'sent_by' => $request->user_id,
            'shop_id' => $request->shop_id,
        ]);


        return response()->json([
            'product' => $success,
        ], 200);
    }

    public function setStoreProductQuantity(Request $request)
    {
        $success = false;

        $prod = Product::find($request->product_id);
        $boxes = $request->quantity_box;
        $pces = $request->quantity_pce;

        if ($prod->items_in_box) {
            $quantity = $pces + ($boxes * $prod->items_in_box);
        } else {
            $quantity = $pces;
        }

        $prod->quantity = $prod->quantity + $quantity;
        $prod->save();

        $success = true;

        //Log mouvement
        if ($success) {
            StockMouvement::create([
                'product_id' => $request->product_id,
                'type' => StockMouvement::STORE_INCREASE,
                'quantity' => $quantity,
                'user_id' => $request->user_id,
            ]);
        }

        return response()->json([
            'product' => $success,
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
