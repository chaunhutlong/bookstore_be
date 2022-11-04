<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Book;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class ShoppingCartController extends Controller
{

    public function getCart()
    {
        $user = auth()->user();
        $cart = Cart::where('user_id', $user->id)->get();
        return response()->json($cart);
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
                $cart = Cart::where('user_id', $user->id)->where('book_id', $request->book_id)->first();
                if ($cart) {
                    $cart->quantity = $cart->quantity + $request->quantity;
                    $cart->save();
                    // update available quantity
                    $book->available_quantity = $book->available_quantity - $request->quantity;
                    $book->save();
                } else {
                    $cart = new Cart();
                    $cart->user_id = $user->id;
                    $cart->book_id = $request->book_id;
                    $cart->quantity = $request->quantity;
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
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function updateCart(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $cart = Cart::where('user_id', $user->id)->where('book_id', $request->book_id)->first();
            if ($cart) {
                $cart->quantity = $request->quantity;
                $cart->save();
                $cartUser = Cart::where('user_id', $user->id)->get();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Update cart successfully',
                    'data' => $cartUser,
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => "Cart not found.",
                ], 400);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function removeFromCart(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $cart = Cart::where('user_id', $user->id)->where('book_id', $request->book_id)->first();
            if ($cart) {
                $cart->delete();
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
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function clearCart()
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            Cart::where('user_id', $user->id)->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Clear cart successfully',
                'data' => [],
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function addCheckedItem(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $cart = Cart::where('user_id', $user->id)::where('book_id', $request->book_id)->first();
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
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function addAllCheckedItem(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
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
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
