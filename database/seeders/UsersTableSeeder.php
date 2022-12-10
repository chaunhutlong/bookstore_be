<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csvFile = fopen(base_path("database/data/users.csv"), "r");
        $firstLine = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== false) {
            if (!$firstLine) {
                User::create([
                    "name" => $data['0'],
                    "email" => $data['1'],
                    "email_verified_at" => $data['2'],
                    "password" => $data['3'],
                    "remember_token" => $data['4'],
                    "deleted_at" => null
                ]);
            }
            $firstLine = false;
        }

        fclose($csvFile);
    }
}