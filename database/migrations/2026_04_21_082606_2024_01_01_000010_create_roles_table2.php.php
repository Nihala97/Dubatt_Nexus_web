<?php
// ─────────────────────────────────────────────────────────────────
// database/migrations/2024_01_01_000010_create_roles_table.php
// ─────────────────────────────────────────────────────────────────
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');                  // e.g. Admin, Manager, User
            $table->string('slug')->unique();         // e.g. admin, manager, user
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        // ── Profiles (Job profiles / designations) ────────────────────
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');                  // e.g. Acid Tester, Receiver
            $table->string('slug')->unique();         // e.g. acid_tester, receiver
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        // ── Modules (system modules/sections) ─────────────────────────
        // Only create if not already present
        if (!Schema::hasTable('modules')) {
            Schema::create('modules', function (Blueprint $table) {
                $table->id();
                $table->string('name');              // e.g. Acid Testing
                $table->string('slug')->unique();     // e.g. acid_testing
                $table->string('description')->nullable();
                $table->string('group')->nullable();  // e.g. MES, Reports, Masters
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // ── Profile ↔ Module permissions ──────────────────────────────
        // Each profile has a default set of permissions per module.
        // These are TEMPLATE permissions — copied to users when assigned a profile.
        Schema::create('profile_module_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profile_id');
            $table->unsignedBigInteger('module_id');
            $table->boolean('can_view')->default(true);
            $table->boolean('can_create')->default(false);
            $table->boolean('can_edit')->default(false);
            $table->boolean('can_delete')->default(false);
            $table->unsignedBigInteger('granted_by')->nullable();
            $table->timestamps();

            $table->unique(['profile_id', 'module_id']);
            $table->foreign('profile_id')->references('id')->on('profiles')->onDelete('cascade');
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
        });

        // ── user_roles pivot ──────────────────────────────────────────
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('role_id');
            $table->timestamps();

            $table->unique(['user_id', 'role_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });

        // ── user_profiles pivot ───────────────────────────────────────
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('profile_id');
            $table->timestamps();

            $table->unique(['user_id', 'profile_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('profile_id')->references('id')->on('profiles')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('profile_module_permissions');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('profiles');
        Schema::dropIfExists('roles');
    }
};