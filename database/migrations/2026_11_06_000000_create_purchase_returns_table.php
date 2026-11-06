<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchase_returns', function (Blueprint $table) {
            // Explicit: the server default may be MyISAM, which ignores transactions and foreign keys
            $table->engine = 'InnoDB';

            $table->id();
            $table->foreignId('purchase_id')->constrained()->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('packing')->nullable();
            $table->integer('qty_return');
            $table->decimal('unit_cost', 12, 2)->default(0);          // net cost per unit on the purchase invoice
            $table->decimal('cut_amount', 12, 2)->default(0);         // retained by supplier, not credited back
            $table->decimal('amount_deducted', 12, 2);                // net credit from supplier
            $table->decimal('total_after_return', 12, 2)->nullable(); // supplier balance after this return
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_returns');
    }
};
