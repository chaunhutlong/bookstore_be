<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public static $uitLocation = [
        'lat' => 10.87,
        'lng' => 106.8
    ];
    public static function cityDistance(City $city) {
        $dX = self::$uitLocation['lat'] - $city->lat;
        $dY = self::$uitLocation['lng'] - $city->lng;
        return sqrt($dX * $dX + $dY * $dY);
    }
}
