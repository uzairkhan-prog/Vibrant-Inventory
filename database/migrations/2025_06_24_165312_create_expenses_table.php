<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop old table first (if exists)
        Schema::dropIfExists('expenses');

        // Create new table
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_name');     // Expense Name
            $table->text('description')->nullable();  // Description
            $table->string('payment_type');     // Payment Type (cash, account name)
            $table->decimal('amount', 15, 2);   // Amount
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
