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

    private static function randString($length)
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

    public static function shippingFee($distance)
    {
        if ($distance <= 10 && $distance > 0) {
            $value = 15000;
        } elseif ($distance <= 30) {
            $value = 15000 + ($distance - 10) * 300;
        } elseif ($distance <= 100) {
            $value = 15000 + 20 * 300 + ($distance - 30) * 100;
        } elseif ($distance <= 500) {
            $value = 15000 + 20 * 300 + 70 * 100 + ($distance - 100) * 50;
        } else {
            $value = 60000;
        }
        return $value;
    }
    /**
     * Display a listing of the resource.
     * @param \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public static function store($order_id, $user)
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        DB::beginTransaction();
        try {
            $address = Address::where('user_id', $user)->where('is_default', true)->value('id');

            $distance = Address::where('id', $address)->value('distance');

            $shipping = Shipping::create(
                [
                    'order_id' => $order_id,
                    'tracking_num' => self::randString(10),
                    'value' => self::shippingFee($distance),
                    'shipping_on' => Carbon::now()->addDays(5),
                    'address_id' => $address,
                    'description' => '',
                ],
            );
            DB::commit();
            return ShippingResource::make($shipping);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $order)
    {
        DB::beginTransaction();
        try {
            $shipping = Shipping::where('order_id', $order)->first();
            if (Shipping::where('order_id', $order)->first()) {
                $validator = Validator::make(
                    $request->all(),
                    [
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

                $shipping->value = ShippingController::shippingFee($distance);
                $shipping->address_id = $request->address_id;
                $shipping->description = $request->description;
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

    /**
     * Update shipping on.
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function updateShippingOn(Request $request, $order)
    {
        DB::beginTransaction();
        try {
            $shipping = Shipping::where('order_id', $order)->first();
            if ($shipping) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'shipping_on' => 'required|date',
                    ]
                );

                if ($validator->fails()) {
                    return response()->json([
                        'status' => 'error',
                        'massage' => $validator->errors()->first()
                    ], 400);
                }

                $shipping->shipping_on = $request->shipping_on;
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
}