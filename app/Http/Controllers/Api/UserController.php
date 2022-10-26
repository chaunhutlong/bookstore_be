<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserInfoResource;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
    //
    public function getProfile()
    {
        $user_info = UserInfo::where('user_id', request()->user()->id)->get();
        return response(['user_info' => new UserInfoResource($user_info), 'message' => 'User info retrieved successfully']);
    }

    public function createProfile(Request $request) {
        $validator = Validator::make($request->all(), [
            'address' => 'max:255',
            'phone_number' => 'max:12',
            'bio' => 'max:255'
        ]);

        if ($validator->fails()) {
            return response(['error' => $validator->errors(), 'Validation Error']);
        }

        $data = $validator->validated();

        $user_info = new UserInfo();
        $user_info['address'] = $data["address"];
        $user_info['phone_number'] = $data["phone_number"];
        $user_info['bio'] = $data["bio"];
        $user_info['user_id']= request()->user()->id;

        $user_info->save();

        return response(['user_info' => new UserInfoResource($user_info), 'message' => 'User info created successfully']);
    }
}
