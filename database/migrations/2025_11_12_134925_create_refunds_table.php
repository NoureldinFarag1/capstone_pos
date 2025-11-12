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
        Schema::create('refunds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sale_id')->index('refunds_sale_id_foreign');
            $table->unsignedBigInteger('sale_item_id')->nullable()->index('refunds_sale_item_id_foreign');
            $table->unsignedBigInteger('item_id')->index('refunds_item_id_foreign');
            $table->integer('quantity_refunded');
            $table->decimal('refund_amount', 10);
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
