<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\UserInfo;

class UserInfosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csvFile = fopen(base_path("database/data/user_infos.csv"), "r");
        $firstLine = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== false) {
            if (!$firstLine) {
                UserInfo::create([
                    "address" => $data['0'],
                    "phone" => $data['1'],
                    "bio" => $data['2'],
                    "avatar" => $data['3'],
                    "user_id" => $data['4'],
                ]);
            }
            $firstLine = false;
        }

        fclose($csvFile);
    }
}