<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Auth\Events\Verified;
use App\Enums\UserRole;

class GoogleController extends Controller
{
    public function loginUrl()
    {
        try {
            return Response::json([
                'url' => Socialite::driver('google')->stateless()->redirect()->getTargetUrl(),
            ]);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 500);
        }
    }

    public function loginCallback(Request $request)
    {

        DB::beginTransaction();
        try {
            $state = $request->input('state');

            parse_str($state, $result);
            $googleUser = Socialite::driver('google')->stateless()->user();

            // $user = User::firstOrCreate(
            //     [ 'email' => $googleUser->email],
            //     ['name' => $googleUser->name]
            // );

            $user = User::where('email', $googleUser->email)->first();
            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                ]);
                $user->roles()->attach(UserRole::User);

                if ($user->markEmailAsVerified()) {
                    event(new Verified($user));
                    // active user in pivot table role_user
                    foreach ($user->roles as $role) {
                        $user->roles()->updateExistingPivot($role->id, ['active' => 1]);
                    }
                }
            }

            $is_active = false;
            foreach ($user->roles as $role) {
                if ($role->pivot->active) {
                    $is_active = true;
                    break;
                }
            }
            unset($user->roles);

            $roles = $user->roles()->get()->pluck('name');

            $token = $user->createToken('auth_token')->plainTextToken;

            DB::commit();
            return response()->json([
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
                'is_active' => $is_active,
                'roles' => $roles
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => $e->getMessage()], 500);
        }
    }
}
