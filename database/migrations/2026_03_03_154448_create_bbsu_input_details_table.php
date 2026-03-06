<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bbsu_input_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bbsu_batch_id');
            $table->string('lot_no');
            $table->decimal('quantity', 15, 4);
            $table->decimal('acid_percentage', 8, 4);

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
        Schema::dropIfExists('bbsu_input_details');
    }
};
