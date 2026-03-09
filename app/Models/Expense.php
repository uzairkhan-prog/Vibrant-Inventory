<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_name_id',
        'payment_type_id',
        'amount',
        'description',
        'created_at',
        'updated_at'
    ];

    // Define relation to PaymentType
    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class, 'payment_type_id');
    }

    public function expenseName()
    {
        return $this->belongsTo(ExpenseName::class, 'expense_name_id');
    }
}
