<?php
// ─────────────────────────────────────────────────────────────────
// database/seeders/RolesProfilesModulesSeeder.php
// Run: php artisan db:seed --class=RolesProfilesModulesSeeder
// ─────────────────────────────────────────────────────────────────
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesProfilesModulesSeeder extends Seeder
{
    public function run(): void
    {
        // ── Roles ──────────────────────────────────────────────────────
        $roles = [
            ['name' => 'Admin', 'slug' => 'admin', 'description' => 'Full system access'],
            ['name' => 'Manager', 'slug' => 'manager', 'description' => 'Management-level access'],
            ['name' => 'User', 'slug' => 'user', 'description' => 'Standard user access with profile-based permissions'],
        ];
        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(['slug' => $role['slug']], array_merge($role, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ── Profiles ───────────────────────────────────────────────────
        $profiles = [
            ['name' => 'Acid Tester', 'slug' => 'acid_tester', 'description' => 'Performs acid testing on incoming ULAB batches'],
            ['name' => 'Receiver', 'slug' => 'receiver', 'description' => 'Handles incoming material receiving'],
            ['name' => 'BBSU Incharge', 'slug' => 'bbsu_incharge', 'description' => 'Incharge of BBSU operations'],
            ['name' => 'Smelting Incharge', 'slug' => 'smelting_incharge', 'description' => 'Manages smelting operations'],
            ['name' => 'Refining Incharge', 'slug' => 'refining_incharge', 'description' => 'Manages refining operations'],
            ['name' => 'IT Incharge', 'slug' => 'it_incharge', 'description' => 'IT administration and system management'],
        ];
        foreach ($profiles as $profile) {
            DB::table('profiles')->updateOrInsert(['slug' => $profile['slug']], array_merge($profile, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ── Modules ────────────────────────────────────────────────────
        $modules = [
            // Masters
            ['name' => 'Materials Master', 'slug' => 'materials', 'group' => 'Masters', 'sort_order' => 10],
            ['name' => 'Suppliers Master', 'slug' => 'suppliers', 'group' => 'Masters', 'sort_order' => 20],
            ['name' => 'Users Management', 'slug' => 'users', 'group' => 'Masters', 'sort_order' => 30],
            // MES
            ['name' => 'Receiving', 'slug' => 'receiving', 'group' => 'MES', 'sort_order' => 110],
            ['name' => 'Acid Testing', 'slug' => 'acid_testing', 'group' => 'MES', 'sort_order' => 120],
            ['name' => 'BBSU', 'slug' => 'bbsu', 'group' => 'MES', 'sort_order' => 125],
            ['name' => 'Smelting', 'slug' => 'smelting', 'group' => 'MES', 'sort_order' => 130],
            ['name' => 'Refining', 'slug' => 'refining', 'group' => 'MES', 'sort_order' => 140],
            // Reports
            ['name' => 'Material Inward Rpt', 'slug' => 'report_material_inward', 'group' => 'Reports', 'sort_order' => 210],
            ['name' => 'Acid Test Status Rpt', 'slug' => 'report_acid_test_status', 'group' => 'Reports', 'sort_order' => 220],
        ];
        foreach ($modules as $module) {
            DB::table('modules')->updateOrInsert(['slug' => $module['slug']], array_merge($module, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}