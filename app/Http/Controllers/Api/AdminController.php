<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    //
    public function getUserList() {
        $users_list = User::all();
        return response()->json(array('users' => $users_list));
    }
}
