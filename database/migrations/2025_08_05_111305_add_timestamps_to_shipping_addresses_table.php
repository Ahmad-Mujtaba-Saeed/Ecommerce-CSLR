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
        Schema::table('shipping_addresses', function (Blueprint $table) {
            if (!Schema::hasColumn('shipping_addresses', 'created_at')) {
                $table->timestamp('created_at')->useCurrent();
            }

            if (!Schema::hasColumn('shipping_addresses', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_addresses', function (Blueprint $table) {
            $table->dropColumn(['created_at', 'updated_at']);
        });
    }
};
