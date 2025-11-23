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
        // Guard against recreation if table already exists (idempotent fix for environments with pre-existing table)
        if (!Schema::hasTable('brand_category')) {
            Schema::create('brand_category', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('brand_id')->index('brand_category_brand_id_foreign');
                $table->unsignedBigInteger('category_id')->index('brand_category_category_id_foreign');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand_category');
    }
};
