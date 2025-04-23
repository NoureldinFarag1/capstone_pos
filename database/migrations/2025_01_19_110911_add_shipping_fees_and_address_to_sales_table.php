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
            // Explicitly set UTF8mb4 charset and collation for proper Arabic language support
            $table->string('address', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable()->after('shipping_fees');
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
