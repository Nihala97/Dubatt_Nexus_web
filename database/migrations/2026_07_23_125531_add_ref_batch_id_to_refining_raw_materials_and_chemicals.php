<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('refining_raw_materials', function (Blueprint $table) {
            $table->unsignedBigInteger('ref_batch_id')->nullable()->after('raw_material_id');
            $table->foreign('ref_batch_id')->references('id')->on('refining_batches')->nullOnDelete();
        });

        Schema::table('refining_chemicals', function (Blueprint $table) {
            $table->unsignedBigInteger('ref_batch_id')->nullable()->after('chemical_id');
            $table->foreign('ref_batch_id')->references('id')->on('refining_batches')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('refining_raw_materials', function (Blueprint $table) {
            $table->dropForeign(['ref_batch_id']);
            $table->dropColumn('ref_batch_id');
        });

        Schema::table('refining_chemicals', function (Blueprint $table) {
            $table->dropForeign(['ref_batch_id']);
            $table->dropColumn('ref_batch_id');
        });
    }
};