<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Auth\Events\Verified;



class EmailVerificationController extends Controller
{
    public function verify(EmailVerificationRequest $request)
    {
        $user = $request->user();
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Already verified'], 200);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
            // active user in pivot table role_user
            foreach ($user->roles as $role) {
                $user->roles()->updateExistingPivot($role->id, ['active' => 1]);
            }
        }

        return response()->json(["msg" => "Email is verified successfully."], 200);
    }

    public function sendVerificationEmail(Request $request)
    {
        $user = $request->user();
        if ($user->hasVerifiedEmail()) {
            return response()->json(["msg" => "Email already verified."], 400);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(["msg" => "Verification link sent on your email id"]);
    }
}
