<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user,
                'token' => $token,
            ], 201);
        } catch (\Exception $ex) {
            return response()->json([
                'message' => 'Error registering user',
                'errors' => $ex->getMessage()
            ], 400);
        }
    }

    /**
     * Login user
     */
    public function login(LoginRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Invalid credentials'
                ], 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token,
            ], 200);
        } catch (\Exception $ex) {
            return response()->json([
                'message' => 'Error logging in',
                'errors' => $ex->getMessage()
            ], 400);
        }
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Logged out successfully'
            ], 200);
        } catch (\Exception $ex) {
            return response()->json([
                'message' => 'Error logging out',
                'errors' => $ex->getMessage()
            ], 400);
        }
    }

    /**
     * Get current user
     */
    public function me(Request $request)
    {
        try {
            return response()->json([
                'user' => $request->user(),
            ], 200);
        } catch (\Exception $ex) {
            return response()->json([
                'message' => 'Error fetching user',
                'errors' => $ex->getMessage()
            ], 400);
        }
    }
}
