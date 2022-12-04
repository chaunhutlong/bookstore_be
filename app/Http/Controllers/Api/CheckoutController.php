<?php

namespace App\Http\Controllers\Api;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Shipping;
use Illuminate\Http\Request;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;
use App\Enums\CheckoutType;
use App\Http\Controllers\Api\DiscountController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\ShoppingCartController;
use App\Models\Discount;


class CheckoutController extends Controller
{
    private function total($user_id)
    {
        $cart = Cart::where('user_id', $user_id)->where('is_checked', 1)->get();
        $total = 0;
        foreach ($cart as $item) {
            $total += $item->price * $item->quantity;
        }
        return $total;
    }

    private function reduceBookQuantity($user_id)
    {
        $cart = Cart::where('user_id', $user_id)->where('is_checked', 1)->get();
        foreach ($cart as $item) {
            BookController::reduce($item->book_id, $item->quantity);
        }
    }

    private function removeFromCart($user_id)
    {
        $cart = Cart::where('user_id', $user_id)->where('is_checked', 1)->get();
        foreach ($cart as $item) {
            ShoppingCartController::deleteAfterCheckout($item->book_id);
        }
    }

    public function confirmPayment(Request $request)
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        try {
            DB::beginTransaction();
            $user_id = auth()->user()->id;
            if (ShoppingCartController::isEmpty($user_id)) {
                return response()->json(['message' => 'Your cart is empty!']);
            }
            $totalPrice = self::total($user_id);
            $discountValue = 0;
            $discount_id = null;
            $shipping_id = $request->shipping_id;
            $shippingValue = Shipping::where('id', $shipping_id)->value('value');
            if ($request->discount_id != null) {
                $discount_id = $request->discount_id;
                if (DiscountController::isAvailable($discount_id) && !DiscountController::isExpired($discount_id)) {
                    $discountValue = Discount::where('id', $discount_id)->value('value');
                    DiscountController::reduce($discount_id);
                }
            }
            $data = [
                'type' => $request->type,
                'status' => PaymentStatus::NotPaid,
                'total_book_price' => $totalPrice,
                'discount_id' => $discount_id,
                'shipping_id' => $shipping_id,
                'total' => $totalPrice - $discountValue + $shippingValue < 0 ? 0 : $totalPrice - $discountValue + $shippingValue,
                'paid_on' => date('Y-m-d H:i:s', time()),
                'description' => $request->description
            ];
            $payment = Payment::create($data);
            $order = OrderController::store($payment->id);
                        self::reduceBookQuantity($user_id);
                        self::removeFromCart($user_id);
            $shipping = ShippingController::store($order->id, '');
            DB::commit();
            return response()->json([
                'payment' => $payment,
                'order' => $order,
                'shipping' => $shipping
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }
    }
}