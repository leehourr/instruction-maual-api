<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\manuals;


class AuthController extends Controller
{
    /**
     * Create User
     * @param request
     * return User
     */

    public function signup(Request $req)
    {
        try {
            $validateUser = Validator::make(
                $req->all(),
                [
                    'name' => 'required',
                    'email' => 'required|unique:users,email',
                    'password' => 'required',
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'valitaion error',
                    'error' => $validateUser->errors()
                ], 401);
            }

            $user = User::create([
                'name' => $req->name,
                'email' => $req->email,
                'password' => Hash::make($req->password),
            ], 200);

            return response()->json([
                'status' => true,
                'message' => 'User Created',
                'token' => $user->createToken('API TOKEN')->plainTextToken
            ], 200);

        } catch (\Throwable $err) {
            return response()->json([
                'status' => false,
                'message' => $err->getMessage()
            ], 500);
        }
    }

    public function login(Request $req)
    {
        try {
            $validateUser = Validator::make($req->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if (!Auth::attempt(($req->only(['email', 'password'])))) {
                return response()->json([
                    'status' => false,
                    'message' => 'invalid credentials'
                ], 401);
            }

            $user = User::where('email', $req->email)->first();

            return response()->json([
                'status' => true,
                'message' => 'logged in successfully',
                'token' => $user->createToken('API TOKEN')->plainTextToken
            ], 200);

        } catch (\Throwable $err) {
            return response()->json([
                'status' => false,
                'message' => $err->getMessage()
            ], 500);
        }
    }

    public function logout(Request $req)
    {
        $req->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully!',
        ], 200);
    }


    /**
     */
    public function __construct()
    {
    }
}
