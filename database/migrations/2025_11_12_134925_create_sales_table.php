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
        Schema::create('sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('item_id')->nullable()->index('sales_item_id_foreign');
            $table->decimal('total_amount');
            $table->enum('refund_status', ['no_refund', 'partial_refund', 'full_refund'])->default('no_refund');
            $table->timestamp('sale_date')->useCurrent();
            $table->timestamps();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed', 'none']);
            $table->decimal('discount_value', 10)->nullable();
            $table->decimal('shipping_fees')->nullable();
            $table->string('address')->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->enum('is_arrived', ['pending', 'arrived'])->nullable();
            $table->decimal('subtotal', 10)->default(0);
            $table->decimal('discount', 10)->default(0);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('customer_id')->nullable()->index('sales_customer_id_foreign');
            $table->integer('display_id')->nullable();
            $table->string('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
