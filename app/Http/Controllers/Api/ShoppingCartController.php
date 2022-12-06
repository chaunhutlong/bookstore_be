<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Book;
use App\Http\Resources\CartResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase\Contract\Storage;


class ShoppingCartController extends Controller
{
    protected $storageUrl;
    protected $bookStorage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
        $this->bookStorage = app('firebase.storage')->getBucket();
        $this->storageUrl = 'books/';
    }

    public function getCart()
    {
        $user = auth()->user();
        $cart = Cart::with('book')->where('user_id', $user->id)->get();
        // get url for book image
        foreach ($cart as $item) {
            $bookImage =  $this->storageUrl . $item->book->book_image;
            $item->book->book_image = $this->bookStorage->object($bookImage);
            if ($item->book->book_image->exists()) {
                $item->book->book_image = $item->book->book_image->signedUrl(new \DateTime('+1 hour'));
            } else {
                $item->book->book_image = null;
            }
        }
        return response()->json(new CartResource($cart), 200);
    }

    public function addToCart(Request $request)
    {
        DB::beginTransaction();
        try {
            $book = Book::findOrFail($request->book_id);
            $user = auth()->user();

            $validator = Validator::make($request->all(), [
                'quantity' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first(),
                ], 400);
            }

            if ($book->available_quantity >= $request->quantity) {
                // check if exist book is
                $cart = Cart::where('user_id', $user->id)->where('book_id', $book->id)->first();
                if ($cart) {
                    // update quantity and price
                    $cart->quantity = $cart->quantity + $request->quantity;
                    $cart->price = $book->price;
                    $cart->save();
                    // update available quantity
                    $book->available_quantity = $book->available_quantity - $request->quantity;
                    $book->save();
                } else {
                    $cart = new Cart();
                    $cart->user_id = $user->id;
                    $cart->book_id = $request->book_id;
                    $cart->quantity = $request->quantity;
                    $cart->price = $book->price;
                    $cart->save();
                    $book->available_quantity = $book->available_quantity - $request->quantity;
                    $book->save();
                }
                DB::commit();
                $cartUser = Cart::where('user_id', $user->id)->get();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Add to cart successfully',
                    'data' => $cartUser,
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => "We don't have that much quantity.",
                ], 400);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function updateCart(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $cart = Cart::where('user_id', $user->id)->where('book_id', $request->book_id)->first();
            if ($cart) {
                $oldQuantity = $cart->quantity;
                $book = Book::findOrFail($request->book_id);
                $validator = Validator::make($request->all(), [
                    'quantity' => 'required|integer|min:1',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => $validator->errors()->first(),
                    ], 400);
                }

                if ($book->available_quantity >= $request->quantity) {
                    $cart->quantity = $request->quantity;
                    $cart->price = $book->price;
                    $cart->save();
                    $book->available_quantity = $book->available_quantity - ($request->quantity - $oldQuantity);
                    $book->save();
                    DB::commit();
                    $cartUser = Cart::where('user_id', $user->id)->get();
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Update cart successfully',
                        'data' => $cartUser,
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => "We don't have that much quantity.",
                    ], 400);
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function removeFromCart(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $cart = Cart::where('user_id', $user->id)->where('book_id', $request->book_id)->first();
            if ($cart) {
                // delete from cart
                $cart->delete();
                // update available quantity
                $book = Book::findOrFail($request->book_id);
                $book->available_quantity = $book->available_quantity + $cart->quantity;
                $book->save();

                DB::commit();

                $cartUser = Cart::where('user_id', $user->id)->get();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Delete item successfully',
                    'data' => $cartUser,
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => "Item not found.",
                ], 400);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function clearCart()
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $cart = Cart::with('book:id,available_quantity')->where('user_id', $user->id)->get();
            if ($cart) {
                foreach ($cart as $item) {
                    $book = Book::findOrFail($item->book_id);
                    $book->available_quantity = $book->available_quantity + $item->quantity;
                    $book->save();
                }
                Cart::where('user_id', $user->id)->delete();
                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Clear cart successfully',
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => "Cart is empty.",
                ], 400);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Clear cart successfully',
                'data' => [],
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function addCheckedItem(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $validator = Validator::make($request->all(), [
                'book_id' => 'required|integer',
                'is_checked' => 'required|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first(),
                ], 400);
            }

            $cart = Cart::where('user_id', $user->id)->where('book_id', $request->book_id)->first();
            if ($cart) {
                $cart->is_checked = $request->is_checked;
                $cart->save();
                DB::commit();
                $cartUser = Cart::where('user_id', $user->id)->get();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Update cart successfully',
                    'data' => $cartUser,
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => "Cart item not found.",
                ], 400);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function addAllCheckedItem(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();

            $validator = Validator::make($request->all(), [
                'is_checked' => 'required|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first(),
                ], 400);
            }

            $cart = Cart::where('user_id', $user->id)->get();
            foreach ($cart as $item) {
                $item->is_checked = $request->is_checked;
                $item->save();
            }
            DB::commit();
            $cartUser = Cart::where('user_id', $user->id)->get();
            return response()->json([
                'status' => 'success',
                'message' => 'Update cart successfully',
                'data' => $cartUser,
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public static function deleteAfterCheckout($book_id)
    {
        $cartItem = Cart::where('book_id', $book_id);
        $cartItem->delete();
    }

    public static function isEmpty($user_id)
    {
        $cartQuantity = Cart::where('user_id', $user_id)->where('is_checked',true)->count();
        if ($cartQuantity == 0) return true;
        return false;
    }
}
