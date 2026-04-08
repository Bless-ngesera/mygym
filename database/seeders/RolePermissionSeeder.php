<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view dashboard',
            'manage classes',
            'manage members',
            'manage instructors',
            'manage earnings',
            'manage reports',
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $instructorRole = Role::firstOrCreate(['name' => 'instructor']);
        $memberRole = Role::firstOrCreate(['name' => 'member']);

        // Assign permissions to admin
        $adminRole->givePermissionTo(Permission::all());

        // Assign permissions to instructor
        $instructorRole->givePermissionTo([
            'view dashboard',
            'manage classes',
            'manage earnings',
        ]);

        // Assign permissions to member
        $memberRole->givePermissionTo([
            'view dashboard',
        ]);

        // Assign roles to existing users based on their role column
        $users = User::all();
        foreach ($users as $user) {
            switch ($user->role) {
                case 'admin':
                    $user->assignRole('admin');
                    break;
                case 'instructor':
                    $user->assignRole('instructor');
                    break;
                case 'member':
                    $user->assignRole('member');
                    break;
            }
        }
    }
}
