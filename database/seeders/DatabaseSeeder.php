<?php

namespace Database\Seeders;

use App\Models\Genre;
use App\Models\Publisher;
use App\Models\User;
use App\Models\Role;
use App\Enums\UserRole;
use App\Models\Address;
use App\Models\Orders;
use App\Models\Book;
use App\Models\Discount;
use App\Models\Province;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use UsersTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UsersTableSeeder::class
        ]);
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
            UserInfosTableSeeder::class, PublisherTableSeeder::class, GenreTableSeeder::class, ProvincesTableSeeder::class, CityTableSeeder::class, AuthorTableSeeder::class, DiscountTableSeeder::class,
            BookTableSeeder::class, BookAuthorTableSeeder::class, CartTableSeeder::class, AddressTableSeeder::class, ShippingTableSeeder::class, PaymentTableSeeder::class, OrderTableSeeder::class, OrderDetailTableSeeder::class
        ]);
    }
}
