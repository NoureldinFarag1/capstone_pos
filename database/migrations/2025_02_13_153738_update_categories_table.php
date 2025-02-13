<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCategoriesTable extends Migration
{
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropColumn('brand_id');
        });
    }

    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
        });
    }
}
