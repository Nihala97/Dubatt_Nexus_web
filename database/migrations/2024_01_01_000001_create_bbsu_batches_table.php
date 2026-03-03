<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bbsu_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_no')->unique();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->date('doc_date');
            $table->string('category');
            $table->string('status')->default('draft'); // draft, submitted, completed
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bbsu_batches');
    }
};
