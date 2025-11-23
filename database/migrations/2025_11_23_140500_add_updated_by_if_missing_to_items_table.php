<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('items') && !Schema::hasColumn('items','updated_by')) {
            Schema::table('items', function (Blueprint $table) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('is_parent');
                // Add FK only if users table exists
                if (Schema::hasTable('users')) {
                    $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
                }
            });
        }
    }
    public function down(): void {
        if (Schema::hasTable('items') && Schema::hasColumn('items','updated_by')) {
            Schema::table('items', function (Blueprint $table) {
                $table->dropForeign(['updated_by']);
                $table->dropColumn('updated_by');
            });
        }
    }
};
