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
        Schema::create('sale_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sale_id')->index('sale_items_sale_id_foreign');
            $table->unsignedBigInteger('item_id')->index('sale_items_item_id_foreign');
            $table->integer('quantity');
            $table->decimal('price', 10)->nullable()->default(0);
            $table->decimal('subtotal', 10)->nullable();
            $table->timestamps();
            $table->integer('refunded_quantity')->default(0);
            $table->boolean('as_gift')->default(false);
            $table->boolean('is_exchanged')->default(false);
            $table->decimal('special_discount', 5)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
