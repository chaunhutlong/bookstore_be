<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shipping;
use App\Models\Order;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ShippingController extends Controller
{

    public function getShipping($order)
    {
        $shipping = Shipping::where('order_id', $order)->first();
        return response()->json($shipping);
    }

    public function createShipping(Request $request, $order)
    {
        DB::beginTransaction();
        try {
            $order = Order::findOrFail($order);

            $validator = Validator::make(
                $request->all(),
                [
                    'name' => 'required|string',
                    'address' => 'required|string',
                    'phone' => 'required|string|min:10|max:10',
                    'shipping_on' => 'required|date|after_or_equal:create_at',
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
                ['order_id' => $order->id],
                $data
            );

            DB::commit();
            return response()->json([
                'status' => 'success',
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