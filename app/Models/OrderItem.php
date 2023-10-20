<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use SoftDeletes;

    protected $fillable =[
        'price',
        'quantity',
        'product_id',
        'order_id'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Not needed
    // public function shopProduct()
    // {
    //     return ShopProduct::with('product')->where('shop_id', $this->order->shop_id)
    //         ->where('product_id', $this->product_id)
    //         ->first();
    // }
}
