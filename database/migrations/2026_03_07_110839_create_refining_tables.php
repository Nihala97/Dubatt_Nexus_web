<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ── refining_batches ──────────────────────────────────────────────
        Schema::create('refining_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_no', 30)->unique();
            $table->string('pot_no', 50)->nullable();
            $table->unsignedBigInteger('material_id')->nullable();
            $table->date('date');
            // LPG
            $table->decimal('lpg_initial', 12, 3)->nullable();
            $table->decimal('lpg_final', 12, 3)->nullable();
            $table->decimal('lpg_consumption', 12, 3)->nullable();
            // Electricity
            $table->decimal('electricity_initial', 12, 3)->nullable();
            $table->decimal('electricity_final', 12, 3)->nullable();
            $table->decimal('electricity_consumption', 12, 3)->nullable();
            // Oxygen
            $table->decimal('oxygen_flow_nm3', 12, 3)->nullable();
            $table->decimal('oxygen_flow_kg', 12, 3)->nullable();
            $table->decimal('oxygen_flow_time', 12, 3)->nullable();
            $table->decimal('oxygen_consumption', 12, 3)->nullable();
            // Totals
            $table->decimal('total_process_time', 12, 3)->nullable();
            // Meta
            $table->string('status', 20)->default('draft');
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        // ── refining_raw_materials ────────────────────────────────────────
        Schema::create('refining_raw_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('refining_batch_id');
            $table->unsignedBigInteger('raw_material_id')->nullable();
            $table->decimal('qty', 12, 3)->default(0);
            // Smelting source tracking (for QTY popup)
            $table->unsignedBigInteger('smelting_batch_id')->nullable();
            $table->string('smelting_batch_no', 30)->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('status', 20)->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        // ── refining_chemicals ────────────────────────────────────────────
        Schema::create('refining_chemicals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('refining_batch_id');
            $table->unsignedBigInteger('chemical_id')->nullable();
            $table->decimal('qty', 12, 3)->default(0);
            // Smelting source tracking
            $table->unsignedBigInteger('smelting_batch_id')->nullable();
            $table->string('smelting_batch_no', 30)->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('status', 20)->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        // ── refining_process_details ──────────────────────────────────────
        Schema::create('refining_process_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('refining_batch_id');
            $table->string('refining_process', 100);
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->decimal('total_time', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('status', 20)->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        // ── refining_finished_goods_blocks ────────────────────────────────
        Schema::create('refining_finished_goods_blocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('refining_batch_id');
            $table->unsignedBigInteger('material_id')->nullable();
            $table->integer('block_sl_no')->default(0);
            $table->decimal('block_weight', 12, 3)->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('status', 20)->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        // ── refining_finished_goods_summary ──────────────────────────────
        Schema::create('refining_finished_goods_summary', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('refining_batch_id');
            $table->unsignedBigInteger('material_id')->nullable();
            $table->decimal('total_qty', 12, 3)->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('status', 20)->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        // ── refining_dross_blocks ─────────────────────────────────────────
        Schema::create('refining_dross_blocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('refining_batch_id');
            $table->unsignedBigInteger('material_id')->nullable();
            $table->integer('block_sl_no')->default(0);
            $table->decimal('block_weight', 12, 3)->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('status', 20)->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        // ── refining_dross_summary ────────────────────────────────────────
        Schema::create('refining_dross_summary', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('refining_batch_id');
            $table->unsignedBigInteger('material_id')->nullable();
            $table->decimal('total_qty', 12, 3)->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('status', 20)->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refining_dross_summary');
        Schema::dropIfExists('refining_dross_blocks');
        Schema::dropIfExists('refining_finished_goods_summary');
        Schema::dropIfExists('refining_finished_goods_blocks');
        Schema::dropIfExists('refining_process_details');
        Schema::dropIfExists('refining_chemicals');
        Schema::dropIfExists('refining_raw_materials');
        Schema::dropIfExists('refining_batches');
    }
};