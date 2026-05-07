<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('stock_ledgers', function (Blueprint $table) {
            // Only add if they don't already exist
            if (!Schema::hasColumn('stock_ledgers', 'status')) {
                $table->tinyInteger('status')->default(1)->after('balance_qty')
                    ->comment('1=posted, 0=reversed');
            }
            if (!Schema::hasColumn('stock_ledgers', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('status');
            }
            if (!Schema::hasColumn('stock_ledgers', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stock_ledgers', function (Blueprint $table) {
            $table->dropColumn(['status', 'is_active', 'updated_by']);
        });
    }
};




































































































































































