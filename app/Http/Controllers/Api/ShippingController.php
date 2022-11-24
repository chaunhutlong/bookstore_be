<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shipping;
use App\Models\Order;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ShippingController extends Controller
{

    private function randomString($length)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characterLen = strlen($characters);
        $randomString = "";
        while ($randomString == "" || Shipping::where('id_shipping', $randomString)->exists()) {
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $characterLen - 1)];
            }
        }
        return $randomString;
    }

    public function getShipping($order)
    {
        $shipping = Shipping::where('order_id', $order)->first();
        return response()->json($shipping);
    }

    public function createShipping(Request $request, $order)
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        DB::beginTransaction();
        try {
            $order = Order::findOrFail($order);

            $validator = Validator::make(
                $request->all(),
                [
                    'name' => 'required|string',
                    'address' => 'required|string',
                    'phone' => 'required|string|min:10|max:10',
                    'description' => 'string',
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'massage' => $validator->errors()->first()
                ], 400);
            }

            $data = $validator->validated();
            $shipping = Shipping::create(
                [
                    'order_id' => $order->id,
                    'id_shipping' => self::randomString(10),
                    'shipping_on' => Carbon::now()->addDays(5),
                ],
                $data
            );

            DB::commit();
            return response()->json([
                'message' => 'Shipping created successfully',
                'shipping' => $shipping
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroyShipping($order)
    {
        $shipping = Shipping::findOrFail($order);
        $shipping->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Shipping deleted successfully',
        ], 200);
    }
}
