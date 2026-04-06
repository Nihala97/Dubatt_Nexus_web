<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * STEP 1 — Run this migration AFTER dropping (or renaming) the old
 * bbsu_output_materials table, which had one wide row per batch.
 *
 * The new structure stores ONE ROW PER OUTPUT MATERIAL per batch,
 * with a fixed material_code that matches the 9 known BBSU outputs.
 *
 * Yield is a stored computed column: output_qty / total_input_qty * 100
 * It is recalculated every time a batch is saved/updated.
 */
return new class extends Migration {
    public function up(): void
    {
        // Drop old wide-column table if it still exists
        Schema::dropIfExists('bbsu_output_materials');

        Schema::create('bbsu_output_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bbsu_batch_id');

            // The 9 fixed output materials — stored as material_code (e.g. '1007')
            $table->string('material_code', 20);

            $table->decimal('qty', 15, 4)->default(0);

            // Yield % = qty / total_input_qty * 100 — auto-calculated on save
            $table->decimal('yield_pct', 8, 4)->default(0);

            $table->integer('status')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('created_by');
            $table->integer('updated_by')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();

            $table->index('bbsu_batch_id');
            $table->index('material_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bbsu_output_materials');
    }
};