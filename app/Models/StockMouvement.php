<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMouvement extends Model
{
    use HasFactory;

    public const CREATE_BILL = 'create_bill';
    public const CANCEL_BILL = 'cancel_bill';
    public const STORE_INCREASE = 'store_increase';
    public const STORE_RETURN = 'store_return';
    public const STORE_DECREASED = 'store_decrease';
    public const SHOP_INCREASE = 'shop_increase';
    public const SHOP_PETIT_DEPOT_INCREASE = 'shop_petit_depot_increase';
    public const SHOP_PETIT_DEPOT_DECREASE = 'shop_petit_depot_decrease';
    public const PETIT_DEPOT_VERS_MAGASIN = 'petit_depot_vers_magasin';
    public const SHOP_EDITED = 'shop_edited';
    public const MANUAL_EDIT = 'manual_edit';
    public const INIT_STOCK = 'init_stock';

    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'shop_id',
        'user_id',
        'quantity_before',
        'quantity_after',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
