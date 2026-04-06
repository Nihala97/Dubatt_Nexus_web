<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // database/migrations/xxxx_xx_xx_add_lpg2_to_refining_batches_table.php

    public function up(): void
    {
        Schema::table('refining_batches', function (Blueprint $table) {
            $table->decimal('lpg2_initial',     12, 3)->nullable()->after('lpg_consumption');
            $table->decimal('lpg2_final',       12, 3)->nullable()->after('lpg2_initial');
            $table->decimal('lpg2_consumption', 12, 3)->nullable()->after('lpg2_final');
        });
    }

    public function down(): void
    {
        Schema::table('refining_batches', function (Blueprint $table) {
            $table->dropColumn(['lpg2_initial', 'lpg2_final', 'lpg2_consumption']);
        });
    }
};
