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
}
