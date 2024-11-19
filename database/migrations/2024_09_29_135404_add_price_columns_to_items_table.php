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
        Schema::table('items', function (Blueprint $table) {
            $table->decimal('buying_price', 10, 2)->after('price');
            $table->decimal('selling_price', 10, 2)->after('buying_price');
            $table->decimal('tax', 5, 2)->default(0)->after('selling_price');
            $table->decimal('applied_sale', 5, 2)->default(0)->after('tax');
        });
    }

    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['buying_price', 'selling_price', 'tax', 'applied_sale']);
        });
    }
};
