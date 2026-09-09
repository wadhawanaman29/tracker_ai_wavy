<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // 🔍 Find user
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'code'    => 'USER_NOT_FOUND',
                'message' => 'Account does not exist with this email'
            ], 404);
        }

        // 🚫 Check user type
        if ($user->user_type == '0') {
            return response()->json([
                'success' => false,
                'code'    => 'ACCESS_DENIED',
                'message' => 'Your account is not allowed to login'
            ], 403);
        }

        // 🔑 Check password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'code'    => 'INVALID_PASSWORD',
                'message' => 'Incorrect password'
            ], 401);
        }

        // 🧹 Remove expired tokens (example: 1 day)
        $user->tokens()->where('created_at', '<', now()->subDay())->delete();

        // 🚫 Prevent multiple device login
        // $existingToken = $user->tokens()->first();
        // if ($existingToken) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Already logged in on another device'
        //     ], 409);
        // }

        // 🔐 Create token
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'success'   => true,
            'message'   => 'Login successful',
            'token'     => $token,
            'user'      => $user
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $user->tokens()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ], 200);
    }
}
