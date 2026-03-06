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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_code')->unique();   // SUP001
            $table->string('supplier_name');                      // Supplier Name
            $table->string('facts_supplier_code')->nullable();
            $table->text('supplier_address')->nullable();
            $table->string('contact_number', 20)->nullable();
            $table->string('supplier_email')->nullable();

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
        Schema::dropIfExists('suppliers');
    }
};
