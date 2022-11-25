<?php

namespace App\Http\Controllers\Api;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;
use App\Enums\CheckoutType;
use App\Http\Controllers\Api\DiscountController;
use App\Models\Discount;


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

    public function confirmPayment(Request $request) {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        try {
            DB::beginTransaction();
            $totalPrice = self::total(auth()->user()->id);
            $discountValue = 0;
            $discount_id = null;
            if ($request->discount_id != null) {
                $discount_id = $request->discount_id;
                if (DiscountController::isAvailable($discount_id)) {
                    $discountValue = Discount::find($discount_id)->value('value');
                    DiscountController::reduce($discount_id);
                }
            }
            $data = [
                'type' => $request->type,
                'status' => PaymentStatus::NotPaid,
                'before_discount' => $totalPrice,
                'discount_id' => $discount_id,
                'after_discount' => $totalPrice - $discountValue,
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
