<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GuardianController extends Controller
{
    /**
     * Log client-side errors to the server for AI analysis
     */
    public function logError(Request $request)
    {
        $data = $request->validate([
            'message' => 'required|string',
            'source' => 'nullable|string',
            'lineno' => 'nullable|integer',
            'colno' => 'nullable|integer',
            'url' => 'nullable|string',
        ]);

        Log::channel('emergency_fatal')->error('FRONTEND_ERROR: ' . json_encode($data));

        return response()->json(['status' => 'logged']);
    }
}
