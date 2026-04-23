<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('user_module_permissions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('module_id');

            $table->boolean('can_view')->default(false);
            $table->boolean('can_create')->default(false);
            $table->boolean('can_edit')->default(false);
            $table->boolean('can_delete')->default(false);

            $table->unsignedBigInteger('granted_by')->nullable();

            $table->timestamps();

            // optional (recommended)
            $table->unique(['user_id', 'module_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_module_permissions');
    }
};
