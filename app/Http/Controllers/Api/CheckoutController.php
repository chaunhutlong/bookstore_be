<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;
use App\Enums\CheckoutType;


class CheckoutController extends Controller
{
    private function total($user_id) {
        $cart = Cart::where('user_id', $user_id)->where('is_checked',1)->get();
        $total = 0;
        foreach ($cart as $item) {
            $total += $item->price * $item->quantity;
        }
        return $total;
    }

    private function randomString($length) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characterLen = strlen($characters);
        $randomString = "";
        while ($randomString == "" || Payment::where('name', $randomString)->exists()) {
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $characterLen-1)];
            }
        }
        return $randomString;
    }

    public function confirmPayment(Request $request) {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        try {
            DB::beginTransaction();
            $data = [
                'name' => self::randomString(10),
                'type' => $request->type,
                'status' => 0,
                'value' => self::total(auth()->user()->id),
                'paid_on' => date('Y-m-d H:i:s', time()),
                'description' => $request->description
            ];
            $payment = Payment::create($data);
            DB::commit();
            return response()->json([
                'payment' => $payment
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }
    }
}