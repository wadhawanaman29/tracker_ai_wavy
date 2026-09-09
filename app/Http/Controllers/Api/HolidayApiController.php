<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Holiday;

class HolidayApiController extends Controller
{
    // ✅ Only Listing
    public function index()
    {
        $holidays = Holiday::orderBy('date', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $holidays
        ]);
    }
}