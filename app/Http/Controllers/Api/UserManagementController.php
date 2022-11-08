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
    public function getUsers()
    {
        try {
            $users = User::with('roles')->get();
            if ($users) {
                return response()->json([
                    'data' => UserResource::collection($users),
                    'message' => 'Retrieved successfully'
                ]);
            }
            return response()->json([
                'message' => 'No users found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUser(User $user)
    {
        return response(['user' => new UserResource($user), 'message' => 'Retrieved successfully'], 200);
    }

    public function activeUser(Request $request)
    {
        try {

            $user = User::find($request->user_id);
            if ($user) {
                $user->roles()->updateExistingPivot($request->role_id, ['active' => true]);
                return response()->json([
                    'message' => 'User activated successfully'
                ]);
            }
            return response()->json([
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 500);
        }
    }

    public function unactiveUser(Request $request)
    {
        try {
            $user = User::find($request->user_id);
            if ($user) {
                $user->roles()->updateExistingPivot($request->role_id, ['active' => false]);
                return response()->json([
                    'message' => 'User unactivated successfully'
                ]);
            }
            return response()->json([
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 500);
        }
    }

    public function assignRole(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::find($request->user_id);
            if ($user) {
                $role = Role::find($request->role_id);
                if ($role) {
                    $user->roles()->attach($role);
                    DB::commit();
                    return response()->json([
                        'message' => 'Role assigned successfully'
                    ]);
                }
                return response()->json([
                    'message' => 'Role not found'
                ], 404);
            }
            return response()->json([
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => $e->getMessage()], 500);
        }
    }

    public function removeRole(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::find($request->user_id);
            if ($user) {
                $role = Role::find($request->role_id);
                if ($role) {
                    $user->roles()->detach($role);
                    DB::commit();
                    return response()->json([
                        'message' => 'Role removed successfully'
                    ]);
                }
                return response()->json([
                    'message' => 'Role not found'
                ], 404);
            }
            return response()->json([
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => $e->getMessage()], 500);
        }
    }
}
