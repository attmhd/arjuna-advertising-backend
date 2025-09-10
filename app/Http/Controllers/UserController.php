<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $users = User::with("roles")
                ->get()
                ->map(function ($user) {
                    $user->role = $user->getRoleNames()->first();
                    unset($user->roles);
                    return $user;
                });
            return response()->json(
                [
                    "status" => "success",
                    "data" => $users,
                    "message" => "Users retrieved successfully",
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "An error occurred",
                ],
                500,
            );
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validate_user = Validator::make($request->all(), [
                "name" => "required|string|max:255",
                "email" => "required|email|unique:users,email",
                "password" => "required|string|min:5",
                "role" => "required|string|exists:roles,name",
            ]);

            if ($validate_user->fails()) {
                return response()->json(
                    [
                        "status" => "error",
                        "message" => $validate_user->errors()->first(),
                    ],
                    422,
                );
            }

            $user = User::create([
                "name" => $request->name,
                "email" => $request->email,
                "password" => bcrypt($request->password),
            ]);

            $user->assignRole($request->role);

            return response()->json(
                [
                    "status" => "success",
                    "data" => $user,
                    "message" => "User created successfully",
                ],
                201,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "An error occurred",
                ],
                500,
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->role = $user->getRoleNames()->first();

            return response()->json(
                [
                    "status" => "success",
                    "data" => $user,
                    "message" => "User retrieved successfully",
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "An error occurred",
                ],
                500,
            );
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json(
                [
                    "status" => "success",
                    "data" => $user,
                    "message" => "User retrieved successfully",
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "An error occurred",
                ],
                500,
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->update($request->all());
            return response()->json(
                [
                    "status" => "success",
                    "data" => $user,
                    "message" => "User updated successfully",
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "An error occurred",
                ],
                500,
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json(
                [
                    "status" => "success",
                    "message" => "User deleted successfully",
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "An error occurred",
                ],
                500,
            );
        }
    }
}
