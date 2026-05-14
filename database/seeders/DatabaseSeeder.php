<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\RolesProfilesModulesSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AuthSeeder::class,
            RolesProfilesModulesSeeder::class,
        ]);
    }
}
