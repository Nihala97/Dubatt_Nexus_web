<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('acid_test_header', function (Blueprint $table) {
            $table->decimal('net_avg_acid_pct', 10, 4)->nullable()->after('avg_pallet_and_foreign_weight');
        });
    }

    public function down()
    {
        Schema::table('acid_test_header', function (Blueprint $table) {
            $table->dropColumn('net_avg_acid_pct');
        });
    }
};
