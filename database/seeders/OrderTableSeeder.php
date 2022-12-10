<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;

class OrderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        while (Order::count() < 10) {
            try {
                Order::factory(1)->create();
            } catch (\Exception $e) {
                //do nothing
            }
        }
    }
}