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
            // tampilkan inventory dengan pagination 10 data per halaman
            $inventories = Inventory::paginate(10);

            return response()->json(
                [
                    "status" => "success",
                    "data" => $inventories->items(),
                    "pagination" => [
                        "current_page" => $inventories->currentPage(),
                        "per_page" => $inventories->perPage(),
                        "total" => $inventories->total(),
                        "last_page" => $inventories->lastPage(),
                        "from" => $inventories->firstItem(),
                        "to" => $inventories->lastItem(),
                        "has_more_pages" => $inventories->hasMorePages(),
                    ],
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
