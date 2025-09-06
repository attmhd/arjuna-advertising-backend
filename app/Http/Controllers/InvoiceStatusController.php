<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InvoiceStatus;

class InvoiceStatusController extends Controller
{
    public function index()
    {
        try {
            $invoiceStatuses = InvoiceStatus::all();
            return response()->json(
                [
                    "status" => "success",
                    "message" => "Invoice statuses retrieved successfully",
                    "data" => $invoiceStatuses,
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

    public function show($id)
    {
        try {
            $invoiceStatus = InvoiceStatus::findOrFail($id);
            return response()->json(
                [
                    "status" => "success",
                    "message" => "Invoice status retrieved successfully",
                    "data" => $invoiceStatus,
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

    public function store(Request $request)
    {
        try {
            $invoiceStatus = InvoiceStatus::create($request->all());
            return response()->json(
                [
                    "status" => "success",
                    "message" => "Invoice status created successfully",
                    "data" => $invoiceStatus,
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

    public function update(Request $request, $id)
    {
        try {
            $invoiceStatus = InvoiceStatus::findOrFail($id);
            $invoiceStatus->update($request->all());
            return response()->json(
                [
                    "status" => "success",
                    "message" => "Invoice status updated successfully",
                    "data" => $invoiceStatus,
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

    public function destroy($id)
    {
        try {
            $invoiceStatus = InvoiceStatus::findOrFail($id);
            $invoiceStatus->delete();
            return response()->json(
                [
                    "status" => "success",
                    "message" => "Invoice status deleted successfully",
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
