<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'agent_id',
        'total_amount',
        'description',
        'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    // Add this relationship for payments linked to this sale
    public function payments()
    {
        return $this->hasMany(CustomerPayment::class, 'sale_id');
    }
}
