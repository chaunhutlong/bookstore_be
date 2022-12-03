<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;
use App\Http\Resources\AddressResource;
use App\Http\Resources\AddressCollection;
use App\Http\Controllers\Api\CityController;
use App\Models\City;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $per_page = request()->input('per_page', 10);

            $addresses = Address::with('user')->where('user_id', Auth()->user()->id)->paginate($per_page);

            return response()->json(new AddressCollection($addresses), 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     * @param  \App\Models\Address  $address_id
     * @return \Illuminate\Http\Response
     */

    public function show($address_id)
    {
        try {
            $address = Address::with('user')->findOrFail($address_id);

            return response()->json(new AddressResource($address), 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Create or Update resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createOrUpdateAddress(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'description' => 'required|string',
                'city_id' => 'required|integer|exists:cities,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors()
                ], 400);
            }

            $distance = CityController::cityDistance(City::find($request->city_id));

            $address = Address::create([
                'name' => $request->name,
                'description' => $request->description,
                'city_id' => $request->city_id,
                'user_id' => Auth::user()->id,
                'distance' => $distance
            ]);
            DB::commit();
            return response()->json(new AddressResource($address), 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param  \App\Models\Address  $address_id
     * @return \Illuminate\Http\Response
     */

    public function destroy($address_id)
    {
        try {
            $address = Address::findOrFail($address_id);
            $address->delete();

            return response()->json(['message' => 'Address deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}