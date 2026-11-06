<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sale item discount changes from a percentage to a flat amount (Rs) per line.
     */
    public function up(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->decimal('discount', 12, 2)->nullable()->change();
        });

        // Convert existing percentage discounts into the equivalent amount
        DB::table('sale_items')
            ->where('discount', '>', 0)
            ->update(['discount' => DB::raw('ROUND(quantity * price * discount / 100, 2)')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('sale_items')
            ->where('discount', '>', 0)
            ->where('price', '>', 0)
            ->where('quantity', '>', 0)
            ->update(['discount' => DB::raw('ROUND(discount * 100 / (quantity * price), 2)')]);

        Schema::table('sale_items', function (Blueprint $table) {
            $table->decimal('discount', 8, 2)->nullable()->change();
        });
    }
};
