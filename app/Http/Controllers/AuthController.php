<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if (! Auth::attempt($credentials)) {

            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = User::where(
            'email',
            $request->email
        )->first();

        $token = $user
            ->createToken('fund-transfer-api')
            ->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    }
}