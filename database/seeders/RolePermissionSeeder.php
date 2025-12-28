<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin role
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        
        // Create super_admin role (as defined in filament-shield.php)
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        
        // Get the admin user
        $admin = User::where('email', 'admin@example.com')->first();
        
        if ($admin) {
            // Assign super_admin role to the admin user
            $admin->assignRole($superAdminRole);
        }
    }
}
