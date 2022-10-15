<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory(10)->create();

        // add roles to the database
        $roles = ['user', 'admin', 'manager', 'superadmin'];

        // loop through each role and add it to the database
        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }

        foreach (User::all() as $user) {
            foreach (Role::all() as $role) {
                $user->roles()->attach($role->id);

            }
        }
    }
}