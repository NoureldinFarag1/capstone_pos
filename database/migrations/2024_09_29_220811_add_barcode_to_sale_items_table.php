<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->string('barcode')->nullable(); // Add this if you want to allow barcodes to be nullable
        });
    }
    
    public function down()
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn('barcode');
        });
    }
};
