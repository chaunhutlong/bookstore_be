<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function getProfile()
    {
        $user = auth()->user();
        $userInfo = UserInfo::where('user_id', $user->id)->first();
        return response(['user' => new UserResource($userInfo), 'message' => 'Retrieved successfully'], 200);
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
            return response(['user_info' => new UserResource($userInfo), 'message' => 'User info created successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => $e->getMessage()], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = request()->user();

            $validator = Validator::make($request->all(), [
                'old_password' => 'required|string',
                'new_password' => 'required|string|min:6',
            ]);

            $data = $validator->validated();

            if (!Hash::check($data['old_password'], $user->password)) {
                return response(['error' => 'Old password is incorrect'], 400);
            }

            $user->password = Hash::make($data['new_password']);
            $user->save();

            DB::commit();
            return response(['message' => 'Password updated successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => $e->getMessage()], 500);
        }
    }
}
