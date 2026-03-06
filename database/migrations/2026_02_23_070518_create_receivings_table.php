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
        Schema::create('receivings', function (Blueprint $table) {
            $table->id();
            $table->date('receipt_date');
            $table->integer('supplier_id');
            $table->integer('material_id');
            $table->decimal('invoice_qty', 10, 2);
            $table->decimal('received_qty', 10, 2);
            $table->string('unit');
            $table->string('vehicle_number');
            $table->string('lot_no')->unique();
            $table->text('remarks')->nullable();

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
        Schema::dropIfExists('receivings');
    }
};
