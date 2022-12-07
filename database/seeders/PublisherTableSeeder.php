<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Publisher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PublisherTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Publisher::factory(5)->create();
    }
}
