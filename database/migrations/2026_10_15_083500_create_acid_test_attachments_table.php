<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('acid_test_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('acid_test_id')->index();
            $table->string('file_path');
            $table->string('file_name');
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        // Migrate any existing attachment from acid_test_header into the new table
        if (Schema::hasColumn('acid_test_header', 'attachment_path')) {
            $existing = DB::table('acid_test_header')
                ->whereNotNull('attachment_path')
                ->where('attachment_path', '!=', '')
                ->get();

            foreach ($existing as $row) {
                DB::table('acid_test_attachments')->insert([
                    'acid_test_id' => $row->id,
                    'file_path'    => $row->attachment_path,
                    'file_name'    => $row->attachment_name ?? basename($row->attachment_path),
                    'created_by'   => $row->updated_by ?? $row->created_by ?? null,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acid_test_attachments');
    }
};
