<?php

namespace App\Http\Controllers;

use App\Models\SumberPelanggan;
use Illuminate\Http\Request;

class SumberPelangganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $sumberPelanggans = SumberPelanggan::all();
            return response()->json([
                "status" => "success",
                "data" => $sumberPelanggans,
                "message" => "Data berhasil diambil",
            ]);
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
            $validatedData = $request->validate([
                "nama" => "required|string|max:255",
                "deskripsi" => "required|string|max:255",
            ]);

            $sumberPelanggan = SumberPelanggan::create($validatedData);

            return response()->json(
                [
                    "status" => "success",
                    "data" => $sumberPelanggan,
                    "message" => "Data berhasil ditambahkan",
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
    public function show(SumberPelanggan $sumberPelanggan)
    {
        return response()->json([
            "status" => "success",
            "data" => $sumberPelanggan,
            "message" => "Data berhasil ditampilkan",
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SumberPelanggan $sumberPelanggan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SumberPelanggan $sumberPelanggan)
    {
        try {
            $validatedData = $request->validate([
                "nama" => "required|string|max:255",
                "deskripsi" => "required|string|max:255",
            ]);

            $sumberPelanggan->update($validatedData);

            return response()->json(
                [
                    "status" => "success",
                    "data" => $sumberPelanggan,
                    "message" => "Data berhasil diupdate",
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
    public function destroy(SumberPelanggan $sumberPelanggan)
    {
        try {
            $sumberPelanggan->delete();

            return response()->json(
                [
                    "status" => "success",
                    "message" => "Data berhasil dihapus",
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
