<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Team;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

                $team = Team::create([
                    'name' => 'developers',
                    'user_id'=>'1',
                    'personal_team'=>'1',
                ]);
                setPermissionsTeamId($team->id);
                // Create Roles
                $superAdminRoleweb = Role::create(['guard_name' => 'web','name' => 'Super Admin']);
                $superAdminRoleapi = Role::create(['guard_name' => 'api','name' => 'Super Admin']);
                $adminRole = Role::create(['guard_name' => 'web','name' => 'Administrator']);
                $adminRole = Role::create(['guard_name' => 'api','name' => 'Administrator']);
                $userRole = Role::create(['guard_name' => 'web','name' => 'User']);
                $userRole = Role::create(['guard_name' => 'api','name' => 'User']);
                $guestRole = Role::create(['guard_name' => 'web','name' => 'Guest']);
                $guestRole = Role::create(['guard_name' => 'api','name' => 'Guest']);

                // Create Permissions
                $permissions = [
                    'Create User',
                    'Manage Roles',
                    'Manage Permissions',
                    'Manage Users',
                    'Manage SNMP',
                    'Add Device',
                    'SSH to Device',
                    'Manage Teams',
                    'Create Teams',
                    'Edit Teams',
                    'Delete Teams'
                ];
        
                foreach ($permissions as $permission) {
                    Permission::create(['guard_name' => 'web','name' => $permission]);
                    Permission::create(['guard_name' => 'api','name' => $permission]);
                }
        
                // Assign Permissions to SuperAdmin Role
                $superAdminRoleweb->givePermissionTo($permissions);
                $superAdminRoleapi->givePermissionTo($permissions);

                // Create SuperAdmin User
                $user=User::create([
                    'name' => 'Developers',
                    'email' => 'developers@example.com',
                    'password' => bcrypt('superadmin'),
                    'current_team_id'=>$team->id
                ])->assignRole('Super Admin');
                $user->teams()->attach($team);
                $user2=User::create([
                    'name' => 'jihad',
                    'email' => 'jihad.ismail.8@gmail.com',
                    'password' => bcrypt('superadmin'),
                    'current_team_id'=>$team->id
                ])->assignRole('Super Admin');
                $user2->teams()->attach($team);

    }
}

