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
                ], 200);
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
                    'errors' => $validateUser->errors(),
                ], 200);
            }

            if (!Auth::attempt(($req->only(['email', 'password'])))) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Incorrect credentials.'
                ], 200);
            }

            $user = User::where('email', $req->email)->first();
            if ($user->status === "banned") {
                return response()->json([
                    'status' => 403,
                    'message' => "Your account have been banned. Contact the admin if there may be any mistakes regarding the issue."
                ], 200);
            }
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
                'message' => $err->getMessage(),
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

    public function getAllUsers(Request $request)
    {
        $role = $request->user();
        if ($role->role != 'admin') {
            return response()->json([
                'status' => 401,
                'message' => 'You are not authorized for this route',
            ], 200);
        }
        $users = User::all()->where('role', 'user')->values();
        return response()->json([
            'status' => 200,
            'users' => $users,
        ], 200);
    }

    public function banUser(Request $request)
    {
        $role = $request->user();
        if ($role->role != 'admin') {
            return response()->json([
                'status' => 401,
                'message' => 'You are not authorized for this route',
            ], 200);
        }
        if ($request->user_id) {
            $user = User::find($request->user_id);
            $user->status = "banned";
            $user->save();
            // $updatedManual = $manual->update(['status', $request->status]);
            return response()->json([
                'status' => 204,
                'banned_user' => $user,
            ], 200);
        }
    }

    public function unbanUser(Request $request)
    {
        $role = $request->user();
        if ($role->role != 'admin') {
            return response()->json([
                'status' => 401,
                'message' => 'You are not authorized for this route',
            ], 200);
        }
        if ($request->user_id) {
            $user = User::find($request->user_id);
            $user->status = "active";
            $user->save();
            // $updatedManual = $manual->update(['status', $request->status]);
            return response()->json([
                'status' => 204,
                'unbanned_user' => $user,
            ], 200);
        }
    }
    /**
     */
    public function __construct()
    {
    }
}
