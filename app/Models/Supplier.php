<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'balance',
    ];

    public function payments()
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function getCurrentBalanceAttribute()
    {
        $paymentsSum = $this->payments()->sum('amount');
        return $this->balance - $paymentsSum;
    }
}
