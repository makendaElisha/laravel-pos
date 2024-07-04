<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class ShopProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'product_id',
        'quantity',
        'buy_price',
        'sell_price',
        'min_quantity',
    ];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($instance) {
            if ($instance->quantity < 0) {
                throw ValidationException::withMessages(['quantity' => "ERROR! Article stock negatif (Prix/pce: $instance->sell_price FC)."]);
            }
        });
    }

    /**
     * Get the shop that owns the ShopProduct
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Get the product that owns the ShopProduct
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
