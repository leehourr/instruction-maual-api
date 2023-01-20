<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection\emptyArray;
use App\Models\manuals;
use Carbon\Traits\ToStringFormat;
use Illuminate\Database\Console\Migrations\StatusCommand;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use App\Http\Requests\StoreManualRequest;
use Illuminate\Support\Manager;

class ManualController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $manual = Manuals::join('users', 'users.id', '=', 'manuals.user_id')->get(['users.name as uploaded_by', 'manuals.*'])->where('status', 'approved')->makeHidden(['status', 'user_id'])->values();

        return response()->json([
            'status' => true,
            'manual' => $manual
        ], 200);
    }

    public function searchManual(string $title)
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreManualRequest $request)
    {
        $manual = Manuals::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Manual succesfully uploaded',
            'manual' => $manual
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\manuals  $manuals
     * @return \Illuminate\Http\Response
     */
    public function show($title)
    {
        $manual = Manuals::all()->where('title', $title)->values();
        if (!$manual) {
            return response()->json([
                'status' => false,
                'message' => 'Manual not found',
                'manual' => $manual,
                'title' => $title
            ], 404);
        }

        return response()->json([
            'status' => true,
            'manual' => $manual,
            'title' => $title
        ], 200);
    }

    //manuals uploaded by each users
    public function manualOfUser(Request $request)
    {
        $manual = Manuals::all()->where('user_id', $request->user_id)->values();
        if ($manual->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'You have not uploaded any manuals',
                'manual' => $manual,
            ], 404);
        }

        return response()->json([
            'status' => true,
            'manual' => $manual,
        ], 200);

    }

    //pending manuals
    public function pendingManuals(Request $request)
    {
        $user_id = $request->user_id;

        $isAdmin = User::FindOrFail($user_id);
        if ($isAdmin->role != 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'Users are not authorized for this route',
            ], 401);
        }

        $manuals = Manuals::join('users', 'users.id', '=', 'manuals.user_id')->get(['users.name as uploaded_by', 'manuals.*'])->where('status', 'pending')->makeHidden('user_id')->values();
        return response()->json([
            'status' => true,
            'pending_manuals' => $manuals,
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\manuals  $manuals
     * @return \Illuminate\Http\Response
     */
    public function edit(manuals $manuals)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\manuals  $manuals
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, manuals $manuals)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\manuals  $manuals
     * @return \Illuminate\Http\Response
     */
    public function destroy(manuals $manuals)
    {
        //
    }
}
