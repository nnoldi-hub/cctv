<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'clients.view', 'clients.manage',
            'offers.view', 'offers.manage',
            'equipment.view', 'equipment.manage',
            'installations.view', 'installations.manage',
            'invoices.view', 'invoices.manage',
            'users.manage',
            'settings.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $roles = [
            'admin' => $permissions,
            'vanzari' => ['clients.view', 'clients.manage', 'offers.view', 'offers.manage'],
            'tehnic' => ['clients.view', 'equipment.view', 'equipment.manage', 'installations.view', 'installations.manage'],
            'suport' => ['clients.view', 'installations.view'],
            'client' => [],
            'client-manager' => [],
        ];

        foreach ($roles as $role => $rolePermissions) {
            $roleModel = Role::findOrCreate($role);
            $roleModel->syncPermissions($rolePermissions);
        }
    }
}
