<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{
    //
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUsers(Request $request)
    {
        try {
            $request->validate([
                'page' => 'integer',
                'per_page' => 'integer',
            ]);
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 10);

            // get user is not admin
            $users = User::with('roles')->whereHas('roles', function ($query) {
                $query->where('id', '!=', UserRole::Admin);
            })->paginate($perPage, ['*'], 'page', $page);


            foreach ($users as $user) {
                foreach ($user->roles as $role) {
                    if ($role->pivot->active == 1) {
                        $user->is_active = true;
                        break;
                    }
                }
            }

            return response()->json(new UserCollection($users), 200);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 500);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function getUser(User $user)
    {
        return response(['user' => new UserResource($user), 'message' => 'Retrieved successfully'], 200);
    }

    /**
     * Active the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function activeUser(Request $request)
    {
        try {
            $user = User::find($request->user_id);

            $role_id = $request->role_id;
            if ($role_id == UserRole::Admin) {
                return response()->json([
                    'message' => 'Cannot unactive admin role'
                ], 403);
            }


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
    /**
     * Unactive the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function unactiveUser(Request $request)
    {
        try {
            $user = User::find($request->user_id);

            $role_id = $request->role_id;
            if ($role_id == UserRole::Admin) {
                return response()->json([
                    'message' => 'Cannot unactive admin role'
                ], 403);
            }

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

    /**
     * Unactive the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
                // check if role is admin, if yes, don't remove
                if ($role->id == UserRole::Admin) {
                    return response()->json([
                        'message' => 'Cannot remove admin role'
                    ], 403);
                }
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
