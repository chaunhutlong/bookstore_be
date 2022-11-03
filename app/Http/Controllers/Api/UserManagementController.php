<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{
    //
    public function getUsers() {
        $users_list = User::all();
        return response()->json(array('users' => $users_list));
    }

    public function getUser(User $user) {
        return response(['user' => new UserResource($user), 'message' => 'Retrieved successfully'], 200);
    }

    public function activeUser(Request $request) {
        $user = User::find($request->id);
        $user->roles()->updateExistingPivot($request->role_id, ['active' => true]);
        return response(['user' => $user, 'role_id' => $request->role_id, 'message' => 'User unactivated successfully']);
    }

    public function unactiveUser(Request $request) {
        $user = User::find($request->id);
        $user->roles()->updateExistingPivot($request->role_id, ['active' => false]);
        return response(['user' => $user, 'role_id' => $request->role_id, 'message' => 'User unactivated successfully']);
    }
}
