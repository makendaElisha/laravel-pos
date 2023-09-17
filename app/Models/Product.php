<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'code',
        'buy_price',
        'sell_price',
        'quantity',
        'min_quantity',
        'items_in_box',
    ];

    /**
     * Get the shopProducts that owns the Product
     *
     */
    public function shopProducts()
    {
        return $this->belongsTo(ShopProduct::class);
    }

    public function productInShop($productId, $shopId)
    {
        $res = ShopProduct::where('shop_id', $shopId)
            ->where('product_id', $productId)
            ->first();

        \Log::info('ID: ');
        \Log::info($res->id ?? 0);

        return $res;
    }

}
