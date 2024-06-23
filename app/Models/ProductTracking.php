<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTracking extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'product_code',
        'product_name',
        'from',
        'from_details',
        'to',
        'to_details',
        'quantity',
        'price',
        'updated_by',
    ];

    /**
     * Get the product that owns the tracking.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who updated the tracking.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
