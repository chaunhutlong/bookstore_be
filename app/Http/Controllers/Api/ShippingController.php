<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use App\Models\Shipping;
use App\Models\Order;
use App\Models\Addresses;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ShippingController extends Controller
{

    private function randString($length)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characterLen = strlen($characters);
        $randString = "BOXO";
        while ($randString == "BOXO" || Shipping::where('tracking_num', $randString)->exists()) {
            for ($i = 0; $i < $length; $i++) {
                $randString .= $characters[rand(0, $characterLen - 1)];
            }
        }
        return $randString;
    }

    public function getShipping($order)
    {
        DB::beginTransaction();
        try {

            // Check if review belongs to user
            if (Order::where('id', $order)->value('user_id') != auth()->user()->id) {
                return response()->json(['message' => 'You are not authorized to get this shipping'], 403);
            }

            $shipping = Shipping::where('order_id', $order)->first();

            $shipping->address_detail = Address::where('id', $shipping->address_id)->value('description');

            return response()->json([
                'message' => 'Get shipping successfully',
                'shipping' => $shipping,
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
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
                    'address' => 'required|interger',
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
                    'order_id' => $order,
                    'tracking_num' => self::randString(10),
                    'value' => (Address::where('id', $request->address_id)->value('distance')) * 1000,
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

    public function deleteShipping($order)
    {
        $shipping = Shipping::findOrFail($order);
        $shipping->delete();
        return response()->json([
            'message' => 'Shipping deleted successfully',
        ], 200);
    }
}