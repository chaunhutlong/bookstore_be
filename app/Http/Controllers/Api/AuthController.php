<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;
use Hash;

class AuthController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum', ['except' => ['login', 'register']]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:55',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        if($validator->fails()){
            return response(['error' => $validator->errors()]);
        }

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);

        $user = User::create($input);
        $success['name'] = $user->name;
        $success['access_token'] = $user->createToken('auth_token')->plainTextToken;

        return $this->sendResponse($success, 'User register successfully.');
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */

    public function login(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if($validator->fails()){
            return response(['error' => $validator->errors()]);
        }

        if (!auth()->attempt($data)) {
            return $this->sendError('Login credentials are invaild.', ['error'=>'Login credentials are invaild']);
        }

        $user = User::where('email', $request['email'])->firstOrFail();
        $success['token'] = $user->createToken('auth_token')->plainTextToken;
        $success['name'] = $user->name;

        return $this->sendResponse($success, 'User login successfully');
    }

    /**
     * Refresh api
     *
     * @return \Illuminate\Http\Response
     */
    public function refresh()
    {
        return $this->sendResponse(auth()->user(), 'User refresh successfully');
    }
}