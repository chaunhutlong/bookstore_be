<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Discount;
use App\Http\Resources\DiscountResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


class DiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * @QA\Get(
     *      path="/discounts",
     *      operationId="getDiscountsList",
     *      tags={"discounts"},
     *      summary="Get list of discounts",
     *      description="Returns list of discounts",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/DiscountResource")
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * ),
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

    /**
     * @QA\Post(
     *      path="/discounts",
     *      operationId="storeDiscount",
     *      tags={"discounts"},
     *      summary="Store new discount",
     *     description="Returns discount data",
     *      @OA\RequestBody(
     *         required=true,
     *        @OA\JsonContent(ref="#/components/schemas/Discount")
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Discount")
     *      ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *     @OA\Response(
     *      response=403,
     *      description="Forbidden"
     * ),
     * )
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'value' => 'required|numeric',
                'start_date' => 'required|date|before_or_equal:end_date',
                'end_date' => 'required|date|after:start_date',
                'quantity' => 'required|integer',
            ]);

            $data = $validator->validated();

            $data['start_date'] = Carbon::createFromFormat('d-m-Y', $data['start_date'])->format('Y-m-d');
            $data['end_date'] = Carbon::createFromFormat('d-m-Y', $data['end_date'])->format('Y-m-d');

            $discount = Discount::create($data);
            DB::commit();

            return response([
                'discount' => new DiscountResource($discount),
                'message' => 'Discount created successfully'
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Discount  $discount
     * @return \Illuminate\Http\Response
     */

    /**
     * @QA\Get(
     *      path="/discounts/{id}",
     *      operationId="getDiscountById",
     *      tags={"discounts"},
     *      summary="Get discount information",
     *      description="Returns discount data",
     *      @OA\Parameter(
     *          name="id",
     *          description="Discount id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/DiscountResource")
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     * )
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

    /**
     * @QA\Put(
     *      path="/discounts/{id}",
     *      tags={"discounts"},
     *      summary="Update existing discount",
     *      description="Returns updated discount data",
     *      operationId="updateDiscount",
     *      @OA\Parameter(
     *          name="id",
     *          description="Discount id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/Discount")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Discount")
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     * )
     */
    public function update(Request $request, Discount $discount)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'value' => 'numeric',
            'start_date' => 'date|before_or_equal:end_date',
            'end_date' => 'date|after:start_date',
            'quantity' => 'min:0'
        ]);

        $data = $validator->validated();


        $data['end_date'] = Carbon::createFromFormat('d-m-Y', $data['end_date'])->format('Y-m-d');
        $data['start_date'] = Carbon::createFromFormat('d-m-Y', $data['start_date'])->format('Y-m-d');

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

    /**
     * @QA\Delete(
     *      path="/discounts/{id}",
     *      tags={"discounts"},
     *      summary="Delete existing discount",
     *      description="Deletes a record and returns no content",
     *      operationId="deleteDiscount",
     *      @OA\Parameter(
     *          name="id",
     *          description="Discount id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="No content"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     * )
     */
    public function destroy(Discount $discount)
    {
        $discount->delete();
        return response(['message' => 'Discount deleted successfully']);
    }
}
