<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GetNonceForSigningController
{
    public function __invoke(Request $request)
    {
        $nonce = 'Loginverzoek DigiFactory - ' . Str::random(32);

        session()->put('nonce', $nonce);

        return response()->json([
            'nonce' => $nonce,
        ]);
    }
}
