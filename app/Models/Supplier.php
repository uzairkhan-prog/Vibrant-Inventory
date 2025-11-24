<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company_name',
        'phone',
        'email',
        'address',
        'balance',
    ];

    public function payments()
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function getCurrentBalanceAttribute()
    {
        $initialBalance = $this->balance ?? 0;
        $paid = $this->payments()->sum('amount');
        return $initialBalance - $paid;
    }
}
