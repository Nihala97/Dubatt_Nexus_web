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
        Schema::create('acid_test_percentage_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('acid_test_id');
            $table->string('pallet_no');
            $table->decimal('gross_weight', 10, 3);
            $table->decimal('net_weight', 10, 3);
            $table->string('ulab_type', 50);
            $table->decimal('initial_weight', 10, 3)->nullable();
            $table->decimal('drained_weight', 10, 3)->nullable();
            $table->decimal('weight_difference', 10, 3)->nullable();
            $table->decimal('avg_acid_pct', 5, 2)->nullable();
            $table->string('remarks')->nullable();

            $table->integer('status')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('created_by');
            $table->integer('updated_by')->default(null)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acid_test_percentage_details');
    }
};
