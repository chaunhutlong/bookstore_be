<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;
use App\Http\Resources\DiscountResource;

class DiscountController extends Controller
{
    public function index()
    {
        $discount = Discount::all();
        return response([
            'discounts' => DiscountResource::collection($discount),
            'message' => 'Retrieved successfully'
        ], 200);
    }
}
