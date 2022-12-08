<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

    public function getCityFromProvince(Request $request)
    {
        $province = $request->input("province");
        $city = City::where('province', $province)->get();
        return response()->json([
            'city' => $city
        ]);
    }

    public function getAllProvince()
    {
        $provinces = City::distinct()->get('province');
        return response()->json([
            'provinces' => $provinces
        ]);
    }
}
