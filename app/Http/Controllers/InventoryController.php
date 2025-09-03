<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $inventories = Inventory::with("unit:id,unit_name")
                ->get()
                ->map(function ($inventory) {
                    $data = $inventory->toArray();
                    $data["unit_name"] = $inventory->unit->unit_name ?? null;
                    unset($data["unit_id"], $data["unit"]);
                    return $data;
                });
            return response()->json(
                [
                    "status" => "success",
                    "data" => $inventories,
                    "message" => "Inventory list retrieved successfully",
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => $e->getMessage(),
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
            $inventory = Inventory::create($request->all());
            return response()->json(
                [
                    "status" => "success",
                    "data" => $inventory,
                    "message" => "Inventory created successfully",
                ],
                201,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Inventory $inventory)
    {
        try {
            return response()->json(
                [
                    "status" => "success",
                    "data" => $inventory,
                    "message" => "Inventory retrieved successfully",
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inventory $inventory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inventory $inventory)
    {
        try {
            $inventory->update($request->all());
            return response()->json(
                [
                    "status" => "success",
                    "data" => $inventory,
                    "message" => "Inventory updated successfully",
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventory $inventory)
    {
        try {
            $inventory->delete();
            return response()->json(
                [
                    "status" => "success",
                    "message" => "Inventory deleted successfully",
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => $e->getMessage(),
                ],
                500,
            );
        }
    }
}
