<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $units = Unit::all();
            return response()->json(
                [
                    "status" => "success",
                    "data" => $units,
                    "message" => "Units retrieved successfully",
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "Failed to retrieve units",
                    "error" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $unit = Unit::create($request->all());
            return response()->json(
                [
                    "status" => "success",
                    "data" => $unit,
                    "message" => "Unit created successfully",
                ],
                201,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "Failed to create unit",
                    "error" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Unit $unit)
    {
        try {
            $unit = Unit::findOrFail($unit->id);
            return response()->json(
                [
                    "status" => "success",
                    "data" => $unit,
                    "message" => "Unit retrieved successfully",
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "Failed to retrieve unit",
                    "error" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit)
    {
        try {
            $unit = Unit::findOrFail($unit->id);
            return response()->json(
                [
                    "status" => "success",
                    "data" => $unit,
                    "message" => "Unit retrieved successfully",
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "Failed to retrieve unit",
                    "error" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        try {
            $validatedData = $request->validate([
                "unit_name" => "required|string|max:255",
            ]);

            $unit->update($validatedData);
            $unit->refresh();

            return response()->json(
                [
                    "status" => "success",
                    "data" => $unit,
                    "message" => "Unit updated successfully",
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "Failed to update unit",
                    "error" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        try {
            $unit->delete();

            return response()->json(
                [
                    "status" => "success",
                    "message" => "Unit deleted successfully",
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "Failed to delete unit",
                    "error" => $e->getMessage(),
                ],
                500,
            );
        }
    }
}
