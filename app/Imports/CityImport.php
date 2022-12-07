<?php
namespace App\Imports;

use App\Models\City;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
class CityImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new City([
            'name' => $row['name'],
            'admin_name' => $row['admin_name'],
            'lat' => $row['lat'],
            'lng' => $row['lng']
        ]);
    }
}
