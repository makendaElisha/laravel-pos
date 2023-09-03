<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopProduct;
use App\Models\StockMouvement;
use App\Models\TransferShopProduct;
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
        \Log::info($request->is_admin);
        if ($request->is_admin == 1) {
            \Log::info('inside if :::::');
            $prod = Product::find($request->product_id);
            $prod->quantity = $prod->quantity + $request->quantity;
            $prod->save();

            $shopTrans = TransferShopProduct::find($request->trans_prod_id);
            $shopTrans->quantity = $shopTrans->quantity - $request->quantity;
            $shopTrans->save();

            $success = true;

            $mouve = StockMouvement::STORE_RETURN;
        } else {
            \Log::info('inside else id:::::');

            $shopProd = ShopProduct::find($request->shop_prod_id);
            $shopProd->quantity = $shopProd->quantity + $request->quantity;
            $shopProd->save();

            \Log::info('prod id:::::');
            \Log::info($request->shop_prod_id);
            \Log::info('shopProd:::::');
            \Log::info($shopProd);


            $shopTrans = TransferShopProduct::find($request->trans_prod_id);
            $shopTrans->quantity = $shopTrans->quantity - $request->quantity;
            $shopTrans->save();

            $success = true;

            $mouve = StockMouvement::SHOP_INCREASE;
        }

        //Log mouvement
        if ($success) {
            StockMouvement::create([
                'product_id' => $request->product_id,
                'type' => $mouve,
                'quantity' => $request->quantity,
                'user_id' => $request->user_id,
                'shop_id' => $request->shop_id,
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
