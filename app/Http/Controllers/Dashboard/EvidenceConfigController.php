<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class EvidenceConfigController extends Controller
{
    public function index()
    {
        return response()->json(['status' => 'ok']);
    }

    public function listIndicators()
    {
        return response()->json([]);
    }

    public function storeIndicator()
    {
        return response()->json([]);
    }

    public function updateIndicator()
    {
        return response()->json([]);
    }

    public function destroyIndicator()
    {
        return response()->json([]);
    }

    public function reorder()
    {
        return response()->json([]);
    }

    public function storeParam()
    {
        return response()->json([]);
    }

    public function updateParam()
    {
        return response()->json([]);
    }

    public function destroyParam()
    {
        return response()->json([]);
    }

    public function copyParams()
    {
        return response()->json([]);
    }
}
