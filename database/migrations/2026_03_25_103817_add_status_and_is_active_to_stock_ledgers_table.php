<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_ledgers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('material_id');
            $table->string('process_type'); // e.g., 'Receiving', 'AcidTesting', 'BBSU', 'Smelting', 'Refining'
            $table->unsignedBigInteger('process_id')->nullable(); // ID of the specific transaction
            $table->string('doc_no')->nullable(); // Batch/Doc Number
            $table->decimal('in_qty', 15, 3)->default(0);
            $table->decimal('out_qty', 15, 3)->default(0);
            $table->decimal('balance_qty', 15, 3)->default(0);
            $table->tinyInteger('status')->default(1);        // 1 = posted
            $table->boolean('is_active')->default(true);      // for revert queries
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index('material_id');
            $table->index(['process_type', 'process_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_ledgers');
    }
};