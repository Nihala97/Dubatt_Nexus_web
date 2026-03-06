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
        Schema::create('bbsu_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_no');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->date('doc_date');
            $table->string('category');

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
        Schema::dropIfExists('bbsu_batches');
    }
};
