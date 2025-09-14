<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            if (!Schema::hasColumn('refunds', 'sale_item_id')) {
                $table->unsignedBigInteger('sale_item_id')->nullable()->after('sale_id');
                $table->foreign('sale_item_id')->references('id')->on('sale_items')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            if (Schema::hasColumn('refunds', 'sale_item_id')) {
                $table->dropForeign(['sale_item_id']);
                $table->dropColumn('sale_item_id');
            }
        });
    }
};
