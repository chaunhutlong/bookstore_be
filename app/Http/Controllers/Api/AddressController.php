<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;
use App\Http\Resources\AddressResource;
use App\Http\Resources\AddressCollection;
use App\Http\Controllers\Api\CityController;
use App\Models\City;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function setDefault($address)
    {
        DB::beginTransaction();
        try {
            // Set all addresses to not default
            $user = auth()->user();
            Address::where('user_id', $user->id)->update(['is_default' => false]);

            // Set address to default
            $address_default = Address::where('user_id', $user->id)->where('id', $address)->first();
            $address_default->is_default = true;
            $address_default->save();

            DB::commit();
            return response()->json(new AddressResource($address_default), 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $per_page = request()->input('per_page', 10);

            $addresses = Address::with('user')->where('user_id', auth()->user()->id)->paginate($per_page);

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
            $address = Address::with('user')->where('user_id', auth()->user()->id)->where('id', $address_id)->first();

            if (!$address) {
                return response()->json(['message' => 'Address not found'], 404);
            }

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
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'phone' => 'required|numeric|digits:10|same:phone',
                'description' => 'required|string',
                'city_id' => 'required|integer|exists:cities,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors()
                ], 400);
            }

            if (Address::where('user_id', $user->id)->count() == 0) {
                $default = true;
            } else {
                $default = false;
            }

            $distance = CityController::cityDistance(City::find($request->city_id));

            $address = Address::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'description' => $request->description,
                'city_id' => $request->city_id,
                'user_id' => $user->id,
                'distance' => $distance,
                'is_default' => $default,
            ]);
            DB::commit();
            return response()->json(new AddressResource($address), 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $address_id)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $address = Address::where('user_id', $user->id)->where('id', $address_id)->first();
            if ($address) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required|string',
                    'phone' => 'required|numeric|digits:10|same:phone',
                    'description' => 'nullable|string',
                    'city_id' => 'required|integer|exists:cities,id',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'error' => $validator->errors()
                    ], 400);
                }

                $distance = CityController::cityDistance(City::find($request->city_id));

                $address->name = $request->name;
                $address->phone = $request->phone;
                $address->description = $request->description;
                $address->city_id = $request->city_id;
                $address->distance = $distance;
                $address->save();

                DB::commit();
                return response()->json(new AddressResource($address), 200);
            } else {
                return response()->json(['message' => 'Address not found'], 404);
            }
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
            if ($address->is_default) {
                $address_setdefault = Address::where('user_id', Auth()->user()->id)->where('is_default', false)->first();
                if ($address_setdefault) {
                    $address_setdefault->is_default = true;
                    $address_setdefault->save();
                }
            }
            $address->delete();

            return response()->json(['message' => 'Address deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
