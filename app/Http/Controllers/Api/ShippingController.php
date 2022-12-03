<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShippingResource;
use App\Models\Address;
use Illuminate\Http\Request;
use App\Models\Shipping;
use App\Models\Order;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;


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

            return response()->json(new ShippingResource($shipping), 200);
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
            if (Shipping::where('order_id', $order)->first() || Order::findOrFail($order) == null) {
                return response()->json(['message' => 'Shipping already exists or Order not found'], 400);
            } else {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'phone' => 'required|numeric|digits:10',
                        'address_id' => 'required|integer|exists:addresses,id',
                        'description' => 'nullable|string',
                    ]
                );

                if ($validator->fails()) {
                    return response()->json([
                        'status' => 'error',
                        'massage' => $validator->errors()->first()
                    ], 400);
                }

                $distance = Address::where('id', $request->address_id)->value('distance');

                if ($distance <= 10 && $distance > 0) {
                    $value = 15000;
                } elseif ($distance <= 30) {
                    $value = 15000 + ($distance - 10) * 500;
                } else {
                    $value = 40000;
                }

                $shipping = Shipping::create(
                    [
                        'order_id' => $order->id,
                        'tracking_num' => self::randString(10),
                        'value' => $value,
                        'shipping_on' => Carbon::now()->addDays(5),
                        'phone' => $request->phone,
                        'address_id' => $request->address_id,
                        'description' => $request->description,
                    ],
                );

                DB::commit();
                return response()->json(new ShippingResource($shipping), 200);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function updateShipping(Request $request, $order)
    {
        DB::beginTransaction();
        try {
            $shipping = Shipping::where('order_id', $order)->first();
            if (Shipping::where('order_id', $order)->first()) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'phone' => 'required|numeric|digits:10|same:phone',
                        'address_id' => 'required|integer|exists:addresses,id',
                        'description' => 'nullable|string',
                    ]
                );

                if ($validator->fails()) {
                    return response()->json([
                        'status' => 'error',
                        'massage' => $validator->errors()->first()
                    ], 400);
                }

                $data = $validator->validated();

                $distance = Address::where('id', $request->address_id)->value('distance');

                if ($distance <= 10 && $distance > 0) {
                    $value = 15000;
                } elseif ($distance <= 30) {
                    $value = 15000 + ($distance - 10) * 500;
                } else {
                    $value = 40000;
                }

                $shipping->value = $value;
                $shipping->phone = $data['phone'];
                $shipping->address_id = $data['address_id'];
                $shipping->description = $data['description'];
                $shipping->save();

                DB::commit();
                return response()->json(new ShippingResource($shipping), 200);
            } else {
                return response()->json(['message' => 'Shipping not found'], 404);
            }
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