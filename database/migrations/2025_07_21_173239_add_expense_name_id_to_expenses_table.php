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
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('expense_name'); // Remove unconditionally

            $table->unsignedBigInteger('expense_name_id')->nullable()->after('id');

            $table->foreign('expense_name_id')
                ->references('id')
                ->on('expense_names')
                ->onDelete('set null'); // 'expense_names' table assumed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['expense_name_id']);
            $table->dropColumn('expense_name_id');

            $table->string('expense_name')->nullable(); // optionally restore the old column
        });
    }
};
