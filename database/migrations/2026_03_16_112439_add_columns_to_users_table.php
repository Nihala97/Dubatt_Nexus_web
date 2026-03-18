<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // stock_ledgers already created in 2026_03_15_200713_create_stock_ledgers_table
        // Only add available_qty to materials if it doesn't exist yet
        if (!Schema::hasColumn('materials', 'available_qty')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->decimal('available_qty', 15, 3)->default(0)->after('unit');
            });
        }
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn('available_qty');
        });
    }
};