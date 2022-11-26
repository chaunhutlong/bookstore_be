<?php

namespace Database\Seeders;

use App\Models\Publisher;
use App\Models\User;
use App\Models\Role;
use App\Enums\UserRole;
use App\Models\Book;
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
        $roles = UserRole::getKeys();
        // loop through each role and add it to the database
        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }

        // active role
        foreach (User::all() as $user) {
            foreach (Role::all() as $role) {
                $user->roles()->attach($role->id);
                $user->roles()->updateExistingPivot($role->id, ['active' => true]);
            }
        }

        // seed books, publishers to the database
        $this->call([
           BookTableSeeder::class, PublisherTableSeeder::class
        ]);
    }
}
