<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;
use App\Http\Resources\DiscountResource;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class DiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $discount = Discount::all();
        return response([
            'discounts' => DiscountResource::collection($discount),
            'message' => 'Retrieved successfully'
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     */
    public function store(Request $request)
    {
        $data = $request->all();
        // $data['start_date'] = Carbon::createFromFormat('d-m-Y', $data->start_date)->format('Y-m-d');
        // $data['end_date'] = Carbon::createFromFormat('d-m-Y', $data->end_date)->format('Y-m-d');
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50',
            'value' => 'required|numeric',
            // 'start_date' => 'required|date_format:d/m/Y|before_or_equal:end_date',
            // 'end_date' => 'required|date_format:d/m/Y|after:start_date',
        ]);

        if ($validator->fails()) {
            return response(['error' => $validator->errors(), 'Validation Error']);
        }
        $data = $validator->Validated();
        $discount = Discount::create($data);
        return response([
            'discount' => new DiscountResource($discount),
            'message' => 'Discount created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Discount  $discount
     * @return \Illuminate\Http\Response
     */
    public function show(Discount $discount)
    {
        return response([
            'discount' => new DiscountResource($discount),
            'message' => 'Retrieved successfully'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Discount  $discount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Discount $discount)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50',
            'value' => 'required',
            'start_date' => 'required|before:end_date',
            'end_date' => 'required|after:start_date',
            'quantity' => 'required'
        ]);
        if ($validator->fails()) {
            return response(['error' => $validator->errors(), 'Validation Error']);
        }
        $data = $validator->validated();
        $discount->update($data);
        return response([
            'discount' => new DiscountResource($discount),
            'message' => 'Updated successfully'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Discount  $discount
     * @return \Illuminate\Http\Response
     */
    public function destroy(Discount $discount)
    {
        $discount->delete();
        return response(['message' => 'Deleted']);
    }
}