<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserInfoResource;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
    public function getProfile()
    {
        $user = auth()->user();
        $userInfo = UserInfo::where('user_id', $user->id)->first();
        return response(['user' => new UserInfoResource($userInfo), 'message' => 'Retrieved successfully'], 200);
    }

    public function createOrUpdateProfile(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();

            $validator = Validator::make($request->all(), [
                'address' => 'string|max:15',
                'phone_number' => 'numeric|digits:10',
                'bio' => 'string|max:255',
                'avatar' => 'string',
            ]);

            $data = $validator->validated();

            // create or update user info
            $userInfo = UserInfo::updateOrCreate(
                ['user_id' => $user->id],
                $data
            );

            DB::commit();
            return response([
                'user_info' => new UserInfoResource($userInfo),
                'message' => 'User info created successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => $e->getMessage()], 500);
        }
    }
}