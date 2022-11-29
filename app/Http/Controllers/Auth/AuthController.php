<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Enums\UserRole;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;


class AuthController extends Controller
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * @OA\Post(
     *      path="/auth/register",
     *      operationId="register",
     *      tags={"auth"},
     *      summary="Register new user",
     *      description="Returns token for new user",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/RegisterUserRequest")
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/UserResource")
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
    public function register(Request $request)
    {
        DB::beginTransaction();
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:55',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8'
            ]);

            $data = $validator->validated();

            $data['password'] = Hash::make($data['password']);

            $user = User::create($data);
            // attach roles
            $user->roles()->attach(UserRole::User);
            DB::commit();

            event(new Registered($user));


            // get active user
            $is_active = false;
            foreach ($user->roles as $role) {
                if ($role->pivot->active) {
                    $is_active = true;
                    break;
                }
            }
            unset($user->roles);

            $token = $user->createToken('auth_token')->plainTextToken;

            // attach roles
            $roles = $user->roles()->get()->pluck('name');


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

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Post(
     *      path="/auth/login",
     *      operationId="login",
     *      tags={"auth"},
     *      summary="Login user",
     *      description="Returns token for user",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/LoginUserRequest")
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/UserResource")
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
    public function login(LoginRequest $request)
    {
        try {

            $request->authenticate();

            $user = $request->user();
            // get active user
            $user = User::with('roles')->find($user->id);
            $is_active = false;
            foreach ($user->roles as $role) {
                if ($role->pivot->active) {
                    $is_active = true;
                    break;
                }
            }
            unset($user->roles);

            $token = $user->createToken('auth_token')->plainTextToken;

            // attach roles
            $roles = $user->roles()->get()->pluck('name');

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
                'is_active' => $is_active,
                'roles' => $roles
            ]);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 500);
        }
    }
}
