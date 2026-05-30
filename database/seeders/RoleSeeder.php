<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $secretary = Role::create(['name' => 'secretary']);
        $orgEditor = Role::create(['name' => 'org_editor']);

        // Create permissions
        $permissions = [
            // Organization permissions
            'view organizations',
            'create organizations',
            'edit organizations',
            'delete organizations',
            'publish organizations',

            // Story permissions
            'view stories',
            'create stories',
            'edit stories',
            'delete stories',
            'publish stories',

            // Resource permissions
            'view resources',
            'create resources',
            'edit resources',
            'delete resources',
            'publish resources',

            // Event permissions
            'view events',
            'create events',
            'edit events',
            'delete events',
            'publish events',

            // Submission permissions
            'view submissions',
            'review submissions',
            'approve submissions',
            'reject submissions',

            // User management
            'manage users',
            'manage tags',
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign all permissions to secretary
        $secretary->givePermissionTo(Permission::all());

        // Assign limited permissions to org_editor
        $orgEditor->givePermissionTo([
            'view organizations',
            'edit organizations',
            'view stories',
            'create stories',
            'edit stories',
            'view resources',
            'create resources',
            'edit resources',
            'view events',
            'view submissions',
        ]);

        // Create default secretary account
        $secretaryUser = User::create([
            'name' => 'Cohort Secretary',
            'email' => 'secretary@cohortapp.com',
            'password' => Hash::make('Secretary@2024!'),
        ]);
        $secretaryUser->assignRole('secretary');
    }
}