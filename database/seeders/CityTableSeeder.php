<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\City;

class CityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csvFile = fopen(base_path("database/data/city.csv"),"r");
        $firstLine = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== false) {
            if (!$firstLine) {
                City::create([
                    "name" => $data['0'],
                    "lat" => $data['1'],
                    "lng" => $data['2'],
                    "province_id" => $data['3']
                ]);
            }
            $firstLine = false;
        }

        fclose($csvFile);
    }
}
