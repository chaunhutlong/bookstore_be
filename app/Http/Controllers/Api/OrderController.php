<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Http\Resources\OrderDetailResource;
use App\Models\OrderDetail;
use Illuminate\Database\Console\DbCommand;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;

class OrderController extends Controller
{
    //
    public function index() {
        $user = auth()->user();
        $orders = Order::with([
            'orderDetails.book:id,name,isbn,price,book_image',
            'payment:id,status,value'
        ])->find($user->id);
        return response(['orders' => new OrderResource($orders), 'message' => 'Retrieved successfully'], 200);
    }

    public function show(Order $order) {
        $orders_details = Order::with([
            'orderDetails.book:id,name,isbn,price,book_image',
            'payment:id,type,status,value',
            'shipping:id,address,phone',
            'discount:id,name,value'
        ])->find($order->id);
        return response(['orders' => new OrderDetailResource($orders_details), 'message' => 'Retrieved successfully'], 200);
    }

    public function store() {
        $user = auth()->user();
        $cart = Cart::where('user_id', $user->id)->first();
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $data = [
            'status' => 0,
            'order_on' => date('Y-m-d H:i:s', time()),
            'shipping_id' => 1,
            'user_id' => $user->id,
            'payment_id' => 4,
            'discount_id' => 1,
            'created_at' => date('Y-m-d H:i:s', time())
        ];
        try {
            DB::beginTransaction();
            $order = Order::create($data);
            $cart->delete();
            DB::commit();
            return response(['order' => new OrderResource($order), 'message' => 'Order created successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Order $order) {
        DB::beginTransaction();
        try {
            $order->delete();
            return response(['message' => 'Order deleted successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => $e->getMessage()], 500);
        }
    }

    public function update() {

    }
}
