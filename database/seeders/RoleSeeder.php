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
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $secretary = Role::firstOrCreate(['name' => 'secretary']);
        $orgEditor = Role::firstOrCreate(['name' => 'org_editor']);

        $permissions = [
            'view organizations', 'create organizations', 'edit organizations',
            'delete organizations', 'publish organizations',
            'view stories', 'create stories', 'edit stories',
            'delete stories', 'publish stories',
            'view resources', 'create resources', 'edit resources',
            'delete resources', 'publish resources',
            'view events', 'create events', 'edit events',
            'delete events', 'publish events',
            'view submissions', 'review submissions', 'approve submissions', 'reject submissions',
            'manage users', 'manage tags', 'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $secretary->givePermissionTo(Permission::all());

        $orgEditor->givePermissionTo([
            'view organizations', 'edit organizations',
            'view stories', 'create stories', 'edit stories',
            'view resources', 'create resources', 'edit resources',
            'view events', 'view submissions',
        ]);

        $secretaryUser = User::firstOrCreate(
            ['email' => 'secretary@cohortapp.com'],
            [
                'name' => 'Cohort Secretary',
                'password' => Hash::make('Secretary@2024!'),
            ]
        );
        $secretaryUser->assignRole('secretary');

        $editorUser = User::firstOrCreate(
            ['email' => 'editor@cohortapp.com'],
            [
                'name' => 'Organisation Editor',
                'password' => Hash::make('Editor@2024!'),
            ]
        );
        $editorUser->assignRole('org_editor');
    }
}