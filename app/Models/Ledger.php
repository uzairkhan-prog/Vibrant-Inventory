<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_id',
        'type',   // customer OR supplier
        'balance',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'reference_id')->where('type', 'customer');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'reference_id')->where('type', 'supplier');
    }
}
