<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OrderDetail;

class OrderDetailTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        while (OrderDetail::count() < 20) {
            try {
                OrderDetail::factory(1)->create();
            } catch (\Exception $e) {
                // do nothing
            }
        }
    }
}
