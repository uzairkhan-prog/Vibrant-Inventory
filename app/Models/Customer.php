<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
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
        return $this->hasMany(CustomerPayment::class);
    }

    public function getCurrentBalanceAttribute()
    {
        $initialBalance = $this->balance ?? 0;
        $paid = $this->payments()->sum('amount');
        return $initialBalance - $paid;
    }
}
