<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // In a new migration file, e.g. 2024_xx_xx_add_refund_status_to_sales_table.php
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->enum('refund_status', ['no_refund', 'partial_refund', 'full_refund'])
                  ->default('no_refund')
                  ->after('total_amount');
        });
    }

    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('refund_status');
        });
    }
};
