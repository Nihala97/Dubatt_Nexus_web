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
        Schema::dropIfExists('bbsu_power_consumption');
    }
};
