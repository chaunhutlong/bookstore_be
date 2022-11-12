<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Http\Resources\OrderDetailResource;
use App\Models\OrderDetail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function store(Request $request) {

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
