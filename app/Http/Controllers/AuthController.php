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
            $roles = $user->getRoleNames();

            return response()->json([
                "success" => true,
                "message" => "Login successful",
                "data" => [
                    "user" => $user,
                    "roles" => $roles,
                    "token" => $token,
                ],
            ]);
        }

        return response()->json(
            [
                "success" => false,
                "message" => "Unauthorized",
                "data" => null,
            ],
            401,
        );
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Unauthorized",
                    "data" => null,
                ],
                401,
            );
        }

        // Opsional: jika ingin logout dari semua device, kirim param "all=true"
        if ($request->boolean("all")) {
            $user->tokens()->delete();
            $message = "Logged out from all devices";
        } else {
            $token = $user->currentAccessToken();
            if ($token) {
                $token->delete();
                $message = "Logged out";
            } else {
                $message = "No active token found";
            }
        }

        return response()->json([
            "success" => true,
            "message" => $message,
            "data" => null,
        ]);
    }
}
