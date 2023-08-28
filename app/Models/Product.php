<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'code',
        'buy_price',
        'sell_price',
        'quantity',
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
}
