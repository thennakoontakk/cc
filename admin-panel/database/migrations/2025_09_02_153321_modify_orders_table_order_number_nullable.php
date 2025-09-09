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
        Schema::table('orders', function (Blueprint $table) {
            // Drop the unique constraint first
            $table->dropUnique(['order_number']);
            // Make order_number nullable
            $table->string('order_number')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Make order_number not nullable and add unique constraint back
            $table->string('order_number')->nullable(false)->change();
            $table->unique('order_number');
        });
    }
};
