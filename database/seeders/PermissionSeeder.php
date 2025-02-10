<?php

namespace Database\Seeders;

use BezhanSalleh\FilamentShield\FilamentShield;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = ['access filament', 'access telescope'];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $panelAdmin = Role::create(['name' => 'Panel Admin']);
        $panelAdmin->syncPermissions($permissions);

        // Super Admin Role
        FilamentShield::createRole();
    }
}
