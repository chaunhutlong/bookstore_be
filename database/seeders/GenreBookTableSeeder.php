<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BookGenre;

class GenreBookTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        while (BookGenre::count() < 10) {
            try {
                BookGenre::factory(1)->create();
            }
            catch (\Exception $e) {
                // do nothing
            }
        }
    }
}
