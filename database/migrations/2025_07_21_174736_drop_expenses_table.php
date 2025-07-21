<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('expenses');
    }

    public function down()
    {
        // Optionally, recreate the table here if rollback is needed
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expense_name_id')->nullable();
            $table->unsignedBigInteger('payment_type_id')->nullable();
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });
    }
};
