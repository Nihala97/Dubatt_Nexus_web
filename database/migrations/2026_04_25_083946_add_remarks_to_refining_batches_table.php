<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('refining_batches', function (Blueprint $table) {
            $table->text('remarks')->nullable()->after('total_process_time');
        });
    }

    public function down(): void
    {
        Schema::table('refining_batches', function (Blueprint $table) {
            $table->dropColumn('remarks');
        });
    }
};