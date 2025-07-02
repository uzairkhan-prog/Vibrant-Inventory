<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'description',
        'payment_type',
        'amount',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
