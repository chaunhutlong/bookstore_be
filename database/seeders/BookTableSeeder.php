<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csvFile = fopen(base_path("database/data/books.csv"), "r");
        $firstLine = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== false) {
            if (!$firstLine) {
                Book::create([
                    "name" => $data['0'],
                    "available_quantity" => $data['1'],
                    "isbn" => $data['2'],
                    "language" => $data['3'],
                    "total_pages" => $data['4'],
                    "price" => $data['5'],
                    "book_image" => $data['6'],
                    "description" => $data['7'],
                    "published_date" => $data['8'],
                    "publisher_id" => $data['9'],
                    "deleted_at" => null,
                ]);
            }
            $firstLine = false;
        }

        fclose($csvFile);
    }
}