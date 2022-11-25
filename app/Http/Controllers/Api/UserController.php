<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *      path="/admin/users",
     *      operationId="getProfile",
     *      tags={"users"},
     *      summary="Get list of users",
     *      description="Returns list of users",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/UserInfoResource")
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function getProfile()
    {
        $user = auth()->user();
        $userInfo = UserInfo::where('user_id', $user->id)->first();
        // return image url from storage
        $userInfo->avatar = $userInfo->avatar ? asset('storage/' . $userInfo->avatar) : null;
        $user->userInfo = $userInfo;
        return response([
            'success' => true,
            'data' => new UserResource($user),
            'message' => 'User profile was successfully retrieved'
        ]);
    }

    public function createOrUpdateProfile(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();

            $validator = Validator::make($request->all(), [
                'address' => 'string|max:255',
                'phone_number' => 'numeric|digits:10',
                'bio' => 'string|max:255',
                'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $data = $validator->validated();

            $userInfo = UserInfo::where('user_id', $user->id)->first();

            // add new avatar to storage and delete old avatar
            if (
                $request->hasFile('avatar') && request()->file('avatar')->isValid()
            ) {
                $avatar = $request->file('avatar');
                $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
                $avatarPath = $avatar->storeAs('avatars', $avatarName);
                $data['avatar'] = $avatarPath;
                $oldAvatar = $userInfo->avatar;
                if ($userInfo->avatar && Storage::disk('public')->exists($oldAvatar)) {
                    Storage::disk('public')->delete($oldAvatar);
                }
            }

            if ($userInfo) {
                $userInfo->update($data);
            } else {
                $userInfo = UserInfo::create([
                    'user_id' => $user->id,
                    'address' => $data['address'],
                    'phone_number' => $data['phone_number'],
                    'bio' => $data['bio'],
                    'avatar' => $data['avatar'],
                ]);
            }

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
