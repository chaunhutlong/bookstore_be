<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Discount;

class DiscountTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csvFile = fopen(base_path("database/data/discounts.csv"), "r");
        $firstLine = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== false) {
            if (!$firstLine) {
                Discount::create([
                    "name" => $data['0'],
                    "value" => $data['1'],
                    "start_date" => $data['2'],
                    "end_date" => $data['3'],
                    "quantity" => $data['4'],
                    "description" => $data['5'],
                    "is_public" => $data['6']
                ]);
            }
            $firstLine = false;
        }

        fclose($csvFile);
    }
}