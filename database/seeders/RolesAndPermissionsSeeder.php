<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // company permissions
        Permission::create(['name' => 'get companies']);
        Permission::create(['name' => 'edit companies']);
        Permission::create(['name' => 'delete companies']);
        Permission::create(['name' => 'create companies']);

        // venue permissions
        Permission::create(['name' => 'get venues']);
        Permission::create(['name' => 'edit venues']);
        Permission::create(['name' => 'delete venues']);
        Permission::create(['name' => 'create venues']);

        // roles
        Role::create(['name' => 'super-admin'])
            ->givePermissionTo(Permission::all());

        Role::create(['name' => 'company-admin'])
            ->givePermissionTo([
                'get companies',
                'edit companies',
                'get venues',
                'edit venues'
            ]);

        Role::create(['name' => 'user'])
            ->givePermissionTo([
                'get venues'
            ]);
    }
}
