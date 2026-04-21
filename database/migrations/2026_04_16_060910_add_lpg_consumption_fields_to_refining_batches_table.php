<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Adds lpg_consumption_ltr and lpg2_consumption_ltr to refining_batches.
 * Back-fills existing rows: ltr = kg × 1.98
 * No other columns are changed.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('refining_batches', function (Blueprint $table) {
            // Add after the existing _consumption columns (nullable — historical rows may not have them)
            $table->decimal('lpg_consumption_ltr', 15, 3)->nullable()->after('lpg_consumption');
            $table->decimal('lpg2_consumption_ltr', 15, 3)->nullable()->after('lpg2_consumption');
        });

        // Back-fill existing rows
        DB::statement('
            UPDATE refining_batches
            SET lpg_consumption_ltr  = ROUND(lpg_consumption  * 1.98, 3)
            WHERE lpg_consumption  IS NOT NULL
        ');
        DB::statement('
            UPDATE refining_batches
            SET lpg2_consumption_ltr = ROUND(lpg2_consumption * 1.98, 3)
            WHERE lpg2_consumption IS NOT NULL
        ');
    }

    public function down(): void
    {
        Schema::table('refining_batches', function (Blueprint $table) {
            $table->dropColumn(['lpg_consumption_ltr', 'lpg2_consumption_ltr']);
        });
    }
};