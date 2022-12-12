<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase\Contract\Storage;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    protected $storageUrl;
    protected $avatarStorage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
        $this->avatarStorage = app('firebase.storage')->getBucket();
        $this->storageUrl = 'avatars/';
    }

    public function getProfile()
    {
        $user = auth()->user();
        $userInfo = UserInfo::where('user_id', $user->id)->first();
        // return image url from storage
        if ($userInfo && $userInfo->avatar) {
            $avatar = $this->storageUrl . $userInfo->avatar;
            $userInfo->avatar = $this->avatarStorage->object($avatar);
            if ($userInfo->avatar->exists()) {
                $userInfo->avatar = $userInfo->avatar->signedUrl(new \DateTime('+1 hour'));
            } else {
                $userInfo->avatar = null;
            }
        }

        $user->userInfo = $userInfo;
        return response()->json(new UserResource($user), 200);
    }

    public function createOrUpdateProfile(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();

            $validator = Validator::make($request->all(), [
                'address' => 'string|max:255',
                'phone' => 'numeric|digits:10',
                'bio' => 'string|max:255',
                'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $data = $validator->validated();

            $userInfo = UserInfo::where('user_id', $user->id)->first();

            // add new avatar to storage and delete old avatar
            if ($request->hasFile('avatar') && request()->file('avatar')->isValid()) {
                // delete old avatar
                if ($userInfo && $userInfo->avatar) {
                    $oldAvatar = $this->storageUrl . $userInfo->avatar;
                    $oldAvatarStorage = $this->storage->getBucket()->object($oldAvatar);
                    if ($oldAvatarStorage->exists()) {
                        $oldAvatarStorage->delete();
                    }
                }
                $avatar = $request->file('avatar');
                $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
                $this->avatarStorage->upload(
                    file_get_contents($avatar),
                    [
                        'name' => $this->storageUrl . $avatarName,
                    ]
                );
                $data['avatar'] = $avatarName;
            }

            if ($userInfo) {
                $userInfo->update($data);
            } else {
                $data['user_id'] = $user->id;
                $userInfo = UserInfo::create($data);
            }

            $user->userInfo = $userInfo;

            DB::commit();
            return response()->json(new UserResource($user), 200);
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
            return response()->json('Password updated successfully', 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => $e->getMessage()], 500);
        }
    }
}
