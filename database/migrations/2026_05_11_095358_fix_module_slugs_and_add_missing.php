<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Ensure every module the sidebar needs exists in DB.
        // updateOrInsert is safe — skips if slug already exists.
        $modules = [
            ['slug' => 'suppliers_master', 'name' => 'Suppliers Master', 'group' => 'Masters', 'sort_order' => 1],
            ['slug' => 'materials_master', 'name' => 'Materials Master', 'group' => 'Masters', 'sort_order' => 2],
            ['slug' => 'receiving', 'name' => 'Receiving', 'group' => 'MES', 'sort_order' => 1],
            ['slug' => 'acid_testing', 'name' => 'Acid Testing', 'group' => 'MES', 'sort_order' => 2],
            ['slug' => 'bbsu', 'name' => 'BBSU', 'group' => 'MES', 'sort_order' => 3],
            ['slug' => 'smelting', 'name' => 'Smelting', 'group' => 'MES', 'sort_order' => 4],
            ['slug' => 'refining', 'name' => 'Refining', 'group' => 'MES', 'sort_order' => 5],
            ['slug' => 'material_inward_rpt', 'name' => 'Material Inward Rpt', 'group' => 'Reports', 'sort_order' => 1],
            ['slug' => 'acid_test_status_rpt', 'name' => 'Acid Test Status Rpt', 'group' => 'Reports', 'sort_order' => 2],
            ['slug' => 'bbsu_dashboard', 'name' => 'BBSU Dashboard', 'group' => 'Reports', 'sort_order' => 3],
            ['slug' => 'smelting_report', 'name' => 'Smelting Report', 'group' => 'Reports', 'sort_order' => 4],
            ['slug' => 'refining_dashboard', 'name' => 'Refining Dashboard', 'group' => 'Reports', 'sort_order' => 5],
            ['slug' => 'user_activity_log', 'name' => 'User Activity Log', 'group' => 'Reports', 'sort_order' => 6],
            ['slug' => 'settings_users', 'name' => 'Settings - Users', 'group' => 'Settings', 'sort_order' => 1],
            ['slug' => 'settings_roles', 'name' => 'Settings - Roles', 'group' => 'Settings', 'sort_order' => 2],
            ['slug' => 'settings_profiles', 'name' => 'Settings - Profiles', 'group' => 'Settings', 'sort_order' => 3],
            ['slug' => 'settings_modules', 'name' => 'Settings - Modules', 'group' => 'Settings', 'sort_order' => 4],
        ];

        foreach ($modules as $m) {
            DB::table('modules')->updateOrInsert(
                ['slug' => $m['slug']],
                array_merge($m, [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    public function down(): void
    {
    }
};