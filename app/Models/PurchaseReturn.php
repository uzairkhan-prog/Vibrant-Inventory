<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    protected $fillable = [
        'purchase_id',
        'supplier_id',
        'product_id',
        'packing',
        'qty_return',
        'unit_cost',
        'cut_amount',
        'amount_deducted',
        'total_after_return',
        'created_at',
        'updated_at',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
