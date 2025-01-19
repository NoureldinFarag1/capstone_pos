<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShippingFeesAndAddressToSalesTable extends Migration
{
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('shipping_fees', 8, 2)->nullable()->after('discount_value');
            $table->string('address')->nullable()->after('shipping_fees');
        });
    }

    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('shipping_fees');
            $table->dropColumn('address');
        });
    }
}
