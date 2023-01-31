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
                    'email' => 'required|email|unique:users',
                    'password' => 'required|min:6',
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => 409,
                    'message' => $validateUser->errors()->getMessages()
                ]);
            }

            $user = User::create([
                'name' => $req->name,
                'email' => $req->email,
                'password' => Hash::make($req->password),
            ], 200);

            return response()->json([
                'status' => 200,
                'message' => 'Account Created!',
                // 'token' => $user->createToken('API TOKEN')->plainTextToken
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
                    'status' => 513,
                    'message' => 'login error',
                    'errors' => $validateUser->errors()
                ]);
            }

            if (!Auth::attempt(($req->only(['email', 'password'])))) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Incorrect credentials.'
                ]);
            }

            $user = User::where('email', $req->email)->first();

            return response()->json([
                'status' => 200,
                'message' => 'logged in successfully',
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
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

    public function getAllUsers()
    {
        $users = User::all()->where('role', 'user');
        return response()->json([
            'status' => 200,
            'users' => $users,
        ], 200);
    }
    /**
     */
    public function __construct()
    {
    }
}
