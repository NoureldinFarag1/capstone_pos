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
        Schema::table('items', function (Blueprint $table) {
            Schema::table('brands', function (Blueprint $table) {
                $table->string('picture')->nullable(); // Add this line
            });
        
            Schema::table('categories', function (Blueprint $table) {
                $table->string('picture')->nullable(); // Add this line
            });
        
            Schema::table('items', function (Blueprint $table) {
                $table->string('picture')->nullable(); // Add this line
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            Schema::table('brands', function (Blueprint $table) {
                $table->dropColumn('picture');
            });
        
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('picture');
            });
        
            Schema::table('items', function (Blueprint $table) {
                $table->dropColumn('picture');
            });
        });
    }
};
