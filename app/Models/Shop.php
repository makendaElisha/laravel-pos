<?php

namespace App\Models;

use App\Models\ShopProduct;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'slug',
    ];

    // /**
    //  * Get all of the products for the Shop
    //  *
    //  * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
    //  */
    // public function products(): HasManyThrough
    // {
    //     return $this->hasManyThrough(ShopProduct::class, Product::class);
    // }

    /**
     * The products that belong to the Shop
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, ShopProduct::class);
    }

    // /**
    //  * Get all of the products for the Shop
    //  *
    //  *
    //  */
    // public function products()
    // {
    //     return ShopProduct::where('shop_id', $this->id)->get();
    // }
}
