<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\complaints;
use Illuminate\Http\Request;
use App\Http\Requests\StoreComplaintRequest;

class ComplaintController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $complaint = Complaints::all();

        return response()->json([
            'status' => true,
            'complaint' => $complaint
        ], 200);
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
    public function store(StoreComplaintRequest $request)
    {
        $complaint = Complaints::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Complaint sent succesfully',
            'complaint' => $complaint
        ], 200);
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\complaints  $complaints
     * @return \Illuminate\Http\Response
     */
    public function show(complaints $complaints)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\complaints  $complaints
     * @return \Illuminate\Http\Response
     */
    public function edit(complaints $complaints)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\complaints  $complaints
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, complaints $complaints)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\complaints  $complaints
     * @return \Illuminate\Http\Response
     */
    public function destroy(complaints $complaints)
    {
        //
    }
}