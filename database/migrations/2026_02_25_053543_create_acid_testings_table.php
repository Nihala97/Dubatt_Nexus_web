<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('acid_test_header', function (Blueprint $table) {
            $table->id();
            $table->string('lot_number');
            $table->date('test_date');
            $table->integer('supplier_id');
            $table->decimal('avg_pallet_weight', 10, 2);
            $table->decimal('foreign_material_weight', 10, 2)->nullable();
            $table->decimal('invoice_qty', 10, 2);
            $table->decimal('received_qty', 10, 2);  //IN HOUSE WEIGH BRIDGE WEIGHT
            $table->string('vehicle_number')->nullable();
            $table->decimal('avg_pallet_and_foreign_weight', 10, 3)->nullable();

            $table->integer('status')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('acid_testings');
    }
};
