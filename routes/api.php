<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\StatisticsController;

Route::get("/hello", function () {
    return response()->json("Hello World");
});

Route::post("/login", [AuthController::class, "login"]);

// Lindungi endpoint berikut dengan Sanctum
Route::middleware("auth:api")->group(function () {
    Route::post("/logout", [AuthController::class, "logout"]);

    // Routes for Karyawan (and Admin)
    Route::middleware("role:Staf|Admin")->group(function () {
        Route::apiResource("/invoice", InvoiceController::class);
        Route::apiResource("/inventory", InventoryController::class);
    });

    // Routes for Admin only
    Route::middleware("role:Admin")->group(function () {
        Route::apiResource("/user", UserController::class);
        // Statistics routes
        Route::get("/statistics/invoice-summary", [
            StatisticsController::class,
            "invoiceSummary",
        ]);
        Route::get("/statistics/invoice-report", [
            StatisticsController::class,
            "invoiceReport",
        ]);
    });
});
