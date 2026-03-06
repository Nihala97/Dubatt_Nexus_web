<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bbsu_output_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bbsu_batch_id');
            $table->decimal('metallic_qty', 15, 4)->default(0);
            $table->decimal('metallic_yield', 8, 4)->default(0);
            $table->decimal('paste_qty', 15, 4)->default(0);
            $table->decimal('paste_yield', 8, 4)->default(0);
            $table->decimal('fines_qty', 15, 4)->default(0);
            $table->decimal('fines_yield', 8, 4)->default(0);
            $table->decimal('pp_chips_qty', 15, 4)->default(0);
            $table->decimal('pp_chips_yield', 8, 4)->default(0);
            $table->decimal('abs_chips_qty', 15, 4)->default(0);
            $table->decimal('abs_chips_yield', 8, 4)->default(0);
            $table->decimal('separator_qty', 15, 4)->default(0);
            $table->decimal('separator_yield', 8, 4)->default(0);
            $table->decimal('battery_plates_qty', 15, 4)->default(0);
            $table->decimal('battery_plates_yield', 8, 4)->default(0);
            $table->decimal('terminals_qty', 15, 4)->default(0);
            $table->decimal('terminals_yield', 8, 4)->default(0);
            $table->decimal('acid_qty', 15, 4)->default(0);
            $table->decimal('acid_yield', 8, 4)->default(0);

            $table->integer('status')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('created_by');
            $table->integer('updated_by')->default(null)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bbsu_output_materials');
    }
};
