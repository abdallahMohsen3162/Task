<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class VerificationController extends Controller
{
    public function verify(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|string|size:6',
        ]);

        $user = $request->user();

        if ($user->verification_code !== $request->verification_code) {
            throw ValidationException::withMessages([
                'verification_code' => ['The verification code is incorrect.'],
            ]);
        }

        $user->is_verified = true;
        $user->verification_code = null;
        $user->save();

        return response()->json(['message' => 'Account verified successfully.']);
    }
}
