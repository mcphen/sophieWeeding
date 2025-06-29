<?php

namespace App\Http\Controllers;

use App\Models\CookieConsent;
use Illuminate\Http\Request;

class CookieConsentController extends Controller
{
    /**
     * Store a new cookie consent record.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Create a new cookie consent record
            CookieConsent::create([
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'consented_at' => now(),
            ]);

            return response()->json([
                'message' => 'Cookie consent recorded successfully',
                'status' => 'success'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to record cookie consent',
                'status' => 'error'
            ], 500);
        }
    }
}
