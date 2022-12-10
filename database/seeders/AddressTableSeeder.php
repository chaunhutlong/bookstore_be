<?php

namespace Database\Seeders;

use App\Models\OrderDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Address;

class AddressTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csvFile = fopen(base_path("database/data/addresses.csv"), "r");
        $firstLine = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== false) {
            if (!$firstLine) {
                Address::create([
                    "name" => $data['0'],
                    "phone" => $data['1'],
                    "distance" => $data['2'],
                    "user_id" => $data['3'],
                    "city_id" => $data['4'],
                    "description" => $data['5'],
                    "is_default" => $data['6'],
                ]);
            }
            $firstLine = false;
        }

        fclose($csvFile);
    }
}