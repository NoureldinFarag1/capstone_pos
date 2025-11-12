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
        Schema::create('items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('parent_id')->nullable()->index('parent_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('brand_id');
            $table->string('name');
            $table->timestamps();
            $table->unsignedBigInteger('updated_by')->nullable()->index('items_updated_by_foreign');
            $table->string('barcode')->nullable();
            $table->string('code')->nullable();
            $table->integer('quantity')->default(0);
            $table->decimal('buying_price')->nullable();
            $table->decimal('selling_price')->nullable();
            $table->string('picture')->nullable();
            $table->decimal('tax')->nullable();
            $table->decimal('applied_sale')->nullable();
            $table->string('discount_type')->nullable()->default('percentage');
            $table->decimal('discount_value')->default(0);
            $table->json('barcodes')->nullable();
            $table->boolean('is_parent')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
