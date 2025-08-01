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
        Schema::table('cart_products', function (Blueprint $table) {
            $table->string('cart_item_id')->after('id');
            
            // Add index for better query performance
            $table->index('cart_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_products', function (Blueprint $table) {
            $table->dropIndex(['cart_item_id']);
            $table->dropColumn('cart_item_id');
        });
    }
};
