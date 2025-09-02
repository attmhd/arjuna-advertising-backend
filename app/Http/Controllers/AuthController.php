<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only("email", "password");

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken("authToken")->plainTextToken;

            return response()->json([
                "user" => $user,
                "token" => $token,
            ]);
        }

        return response()->json(["message" => "Unauthorized"], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(["message" => "Logged out"]);
    }
}
