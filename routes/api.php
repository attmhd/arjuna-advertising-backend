<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\InventoryController;

Route::get("/hello", function () {
    return response()->json("Hello World");
});

Route::post("/login", [AuthController::class, "login"]);

// Lindungi endpoint berikut dengan Sanctum
Route::middleware("auth:sanctum")->group(function () {
    Route::post("/logout", [AuthController::class, "logout"]);
});

Route::apiResource("/user", UserController::class);
Route::apiResource("/unit", UnitController::class);
Route::apiResource("/inventory", InventoryController::class);
