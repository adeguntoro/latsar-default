<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class UserRoleMaker extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Create roles
        foreach (config('temukpu.roles') as $role){
            Role::firstOrCreate(['name' => $role]);
        }
        
        foreach (config('temukpu.permissions') as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign 'create confidential files' permission to kasubag and komisioner roles
        $kasubagRole = Role::findByName('kasubag');
        $komisionerRole = Role::findByName('komisioner');
        $confidentialPermission = Permission::findByName('create confidential files');
        
        if ($kasubagRole && $confidentialPermission) {
            $kasubagRole->givePermissionTo($confidentialPermission);
        }
        if ($komisionerRole && $confidentialPermission) {
            $komisionerRole->givePermissionTo($confidentialPermission);
        }
        
        // Create or update users with roles (using firstOrCreate to avoid duplicates)


        $users = [
            ['name' => 'Super Admin', 'email' => 'superadmin@gmail.com', 'role' => 'Superadmin'],
            ['name' => 'Staff', 'email' => 'staff@gmail.com', 'role' => 'staff'],
            ['name' => 'Kasubag', 'email' => 'kasubag@gmail.com', 'role' => 'kasubag'],
            ['name' => 'Komisioner', 'email' => 'komisioner@gmail.com', 'role' => 'komisioner'],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => bcrypt('password'),
                ]
            );

            $user->assignRole($data['role']);
        }
        // $superadmin = User::firstOrCreate(
        //     ['email' => 'superadmin@gmail.com'],
        //     [
        //         'name' => 'Super Admin',
        //         'password' => bcrypt('password'),
        //     ]
        // );
        // $superadmin->assignRole('Superadmin');
        
        // $staff = User::firstOrCreate(
        //     ['email' => 'staff@gmail.com'],
        //     [
        //         'name' => 'Staff',
        //         'password' => bcrypt('password'),
        //     ]
        // );
        // $staff->assignRole('staff');

        // $kasubag = User::firstOrCreate(
        //     ['email' => 'kasubag@gmail.com'],
        //     [
        //         'name' => 'Kasubag',
        //         'password' => bcrypt('password'),
        //     ]
        // );
        // $kasubag->assignRole('kasubag');

        // $komisioner = User::firstOrCreate(
        //     ['email' => 'komisioner@gmail.com'],
        //     [
        //         'name' => 'Komisioner',
        //         'password' => bcrypt('password'),
        //     ]
        // );
        // $komisioner->assignRole('komisioner');

        /**
         * noted :
         * each user can change their profile information, but only super admin can manage 
         * users, roles, and permissions, and create user
         *
         * staff and kasubag can edit, delete, and create internal post
         * komisioner and kasubag can edit, delete, and create confidential files
         *
         * 'publik', 'internal', 'rahasia' type of posts
         * confidential is rahasia
         * every user can download publik,
         * only auth user can access internal
         * only kasubag and komisioner can access rahasia
         */

    }
}