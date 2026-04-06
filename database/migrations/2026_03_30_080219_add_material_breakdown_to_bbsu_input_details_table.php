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
        Schema::table('bbsu_input_details', function (Blueprint $table) {
            $table->json('material_breakdown')->nullable()->after('acid_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bbsu_input_details', function (Blueprint $table) {
            $table->dropColumn('material_breakdown');
        });
    }
};
