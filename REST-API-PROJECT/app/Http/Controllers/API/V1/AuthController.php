<?php

namespace App\Http\Controllers\API\V1;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
                'message' => 'Validation Error',
                'status' => 422
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password
        ]);

        $token = $user->createToken('ABC_123')->plainTextToken;
        return response()->json([
            'user' => $user,
            'message' => 'User register successfully',
            'status' => 201,
            'token' => $token
        ], 201);
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
                'message' => 'Validation Error',
                'status' => 422
            ], 422);
        }

        if (!Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'message' => 'Unauthorize',
                'status' => 401
            ], 401);
        }

        $user = User::where('email', $request->email)->first();
        $token = $user->createToken('ABC_123')->plainTextToken;

        return response()->json([
            'user' => $user,
            'message' => 'User Login Successfully',
            'status' => 200,
            'token' => $token
        ], 200);
    }
    public function logout()
    {
        // Delete current token
        Auth::user()->currentAccessToken()->delete();

        // Delete all token
        // Auth::user()->token()->delete();

        return response()->json([
            'message' => 'User logged out Successfully',
            'status' => 200
        ], 200);
    }
}
