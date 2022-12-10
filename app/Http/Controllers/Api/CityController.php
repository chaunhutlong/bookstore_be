<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Province;
use Illuminate\Http\Request;
use App\Models\City;

class CityController extends Controller
{
    public static $uitLocation = [
        'lat' => 10.87,
        'lng' => 106.8
    ];

    public static function cityDistance(City $city)
    {
        $dX = self::$uitLocation['lat'] - $city->lat;
        $dY = self::$uitLocation['lng'] - $city->lng;
        return 100 * sqrt($dX * $dX + $dY * $dY);
    }

    public function getCityFromProvince(Province $province)
    {
        $province_id = $province->id;
        $city = City::where('province_id', $province_id)->get();
        return response()->json([
            'city' => $city
        ]);
    }

    public function getAllProvince()
    {
        $provinces = Province::all();
        return response()->json([
            'provinces' => $provinces
        ]);
    }
}
