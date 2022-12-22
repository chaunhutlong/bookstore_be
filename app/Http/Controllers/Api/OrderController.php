<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Http\Resources\OrderDetailResource;
use App\Models\Discount;
use App\Models\Shipping;
use Illuminate\Database\Console\DbCommand;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;
use Kreait\Firebase\Contract\Storage;


class OrderController extends Controller
{
    protected $storageUrl;
    protected $bookStorage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
        $this->bookStorage = app('firebase.storage')->getBucket();
        $this->storageUrl = 'books/';
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->input('per_page', 10);

        $orders = Order::with([
            'orderDetails.book:id,name,isbn,price,book_image',
            'payment:id,type,status,total'
        ])->where('user_id', $user->id)->where('is_deleted', false)->paginate($perPage);

        // return image url
        $books = [];

        foreach ($orders as $order) {
            if (!$order->orderDetails->isEmpty()) {
                // loop through order details array
                // check duplicate book id in order details

                foreach ($order->orderDetails as $order_list) {
                    // check duplicate book id in order details
                    // if duplicate book id, just return image url 
                    if (!in_array($order_list->book->id, $books)) {
                        $books[] = $order_list->book->id;
                        $bookImage = $this->storageUrl . $order_list->book->book_image;
                        $order_list->book->book_image = $this->bookStorage->object($bookImage);
                        if ($order_list->book->book_image->exists()) {
                            $order_list->book->book_image = $order_list->book->book_image->signedUrl(new \DateTime('+1 hour'));
                        } else {
                            $order_list->book->book_image = null;
                        }
                    }
                }
            }
        }
        return response()->json(new OrderCollection($orders), 200);
    }

    public function allOrders(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $orders = Order::with([
            'orderDetails.book:id,name,isbn,price,book_image',
            'payment:id,type,status,total'
        ])->paginate($perPage);

        $books = [];
        foreach ($orders as $order) {
            if (!$order->orderDetails->isEmpty()) {
                // loop through order details array
                // check duplicate book id in order details

                foreach ($order->orderDetails as $order_list) {
                    // check duplicate book id in order details
                    // if duplicate book id, just return image url 
                    if (!in_array($order_list->book->id, $books)) {
                        $books[] = $order_list->book->id;
                        $bookImage = $this->storageUrl . $order_list->book->book_image;
                        $order_list->book->book_image = $this->bookStorage->object($bookImage);
                        if ($order_list->book->book_image->exists()) {
                            $order_list->book->book_image = $order_list->book->book_image->signedUrl(new \DateTime('+1 hour'));
                        } else {
                            $order_list->book->book_image = null;
                        }
                    }
                }
            }
        }

        return response()->json(new OrderCollection($orders), 200);
    }

    public function show(Order $order)
    {
        $orders_details = Order::with([
            'orderDetails.book:id,name,isbn,price,book_image',
            'payment'
        ])->find($order->id);

        // check empty order_details
        foreach ($orders_details as $order_detail) {
            if (!$order_detail->orderDetails->isEmpty()) {
                foreach ($orders_details->orderDetails as $book) {
                    $bookImage = $this->storageUrl . $book->book_image;
                    $book->book_image = $this->bookStorage->object($bookImage);
                    if ($book->book_image->exists()) {
                        $book->book_image = $book->book_image->signedUrl(new \DateTime('+1 hour'));
                    } else {
                        $book->book_image = null;
                    }
                }
            }
        }

        $discount = Discount::where('id', $orders_details->payment->discount_id)->first(['name', 'value']);
        $shipping = Shipping::where('id', $orders_details->payment->shipping_id)->first(['name', 'address_id', 'phone', 'value', 'shipping_on']);
        $orders_details->shipping = $shipping;
        $orders_details->discount = $discount;
        return response()->json(new OrderDetailResource($orders_details), 200);
    }

    public static function store($payment_id)
    {
        $user = auth()->user();
        $data = [
            'status' => 0,
            'order_on' => date('Y-m-d H:i:s', time()),
            'user_id' => $user->id,
            'payment_id' => $payment_id,
            'is_deleted' => false
        ];
        try {
            $order = Order::create($data);
            $cart = Cart::where('user_id', $user->id)->where('is_checked', 1)->get();
            $order_detail = [];
            foreach ($cart as $item) {
                $data = [
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'order_id' => $order->id,
                    'book_id' => $item->book_id
                ];
                $order_detail[] = OrderDetail::create($data);
            }
            return collect([$order, $order_detail]);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Order $order)
    {
        DB::beginTransaction();
        try {
            $order->update([
                'is_deleted' => true,
                'deleted_at' => date('Y-m-d H:i:s', time())
            ]);
            DB::commit();
            return response(['message' => 'Order deleted successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request, Order $order)
    {
        $order = Order::find($order->id);
        $status = $request->status;
        try {
            $order->update(['status' => $status]);
            return response(['order' => Order::find($order->id)]);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 500);
        }
    }
}
