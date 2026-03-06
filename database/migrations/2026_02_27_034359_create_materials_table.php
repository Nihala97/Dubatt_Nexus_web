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
        Schema::create('materials', function (Blueprint $table) {
            $table->id();

            $table->string('material_code')->unique();  
            $table->string('material_name');                   
            $table->string('secondary_name')->nullable();        
            $table->string('stock_code')->nullable();
            $table->string('category')->nullable();
            $table->string('section')->nullable();
            $table->string('unit')->nullable();;                    


            $table->integer('status')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('created_by')->default(null)->nullable();
            $table->integer('updated_by');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
