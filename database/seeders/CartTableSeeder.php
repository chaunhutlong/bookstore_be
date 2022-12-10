<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cart;

class CartTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        while (Cart::count() < 10) {
            try {
                Cart::factory(1)->create();
            }
            catch (\Exception $e) {
                // do nothing
            }
        }
    }
}
