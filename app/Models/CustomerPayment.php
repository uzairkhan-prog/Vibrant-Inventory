<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'date',
        'description',
        'payment_mode',
        'amount',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
