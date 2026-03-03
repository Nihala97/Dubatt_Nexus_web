<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bbsu_power_consumption', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bbsu_batch_id');
            $table->decimal('initial_power', 15, 4)->default(0);
            $table->decimal('final_power', 15, 4)->default(0);
            $table->decimal('total_power_consumption', 15, 4)->default(0);
            $table->string('status')->default('active');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('bbsu_batch_id')->references('id')->on('bbsu_batches')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bbsu_power_consumption');
    }
};
