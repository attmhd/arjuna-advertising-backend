<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get("/hello", function () {
    return response()->json("Hello World");
});
Route::post("/login", [AuthController::class, "login"]);
Route::middleware("auth:sanctum")->post("/logout", [
    AuthController::class,
    "logout",
]);

Route::get("/user", function (Request $request) {
    return $request->user();
})->middleware("auth:sanctum");
