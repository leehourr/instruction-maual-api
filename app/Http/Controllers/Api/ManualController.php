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
use PhpParser\Node\Expr\Cast\Object_;
use Illuminate\Database\Eloquent\Builder;

class ManualController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $manual = Manuals::orderBy("updated_at", "desc")->join('users', 'users.id', '=', 'manuals.user_id')->get(['users.name as uploaded_by', 'manuals.*'])->where('status', 'approved')->makeHidden(['status', 'user_id'])->values();

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
        $user_id = $request->user()->id;
        $newManual = array("user_id" => $user_id, "img_path" => $request->img_path, "title" => $request->title, "description" => $request->description);
        // $newManual["user_id"] = $user_id;
        // $newManual->title = $request->title;
        // $newManual->img_path = $request->img_path;
        // $newManual->description = $request->description;

        $manual = Manuals::create($newManual);

        return response()->json([
            'status' => true,
            'message' => 'Manual succesfully uploaded',
            // 'manual' => $manual,
            'uid' => $manual,
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
        // $manual = Manuals::all()->where('title', 'LIKE', '%' . $title . '%')->values();
        Builder::macro('whereLike', function (string $attribute, string $searchTerm) {
            return $this->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
        });
        $manual = Manuals::query()
            ->whereLike('title', $title)->where("status", "approved")
            ->get();
        // $sManual = collect(["title" => $manual->title]);

        if (!$manual) {
            return response()->json([
                'status' => 404,
                'message' => 'Manual not found',
                'manual' => $manual,
                'title' => $title
            ]);
        }

        return response()->json([
            'status' => 200,
            'manual' => $manual,
            'title' => $title
        ], 200);
    }


    public function allManuals($title)
    {
        // $manual = Manuals::all()->where('title', 'LIKE', '%' . $title . '%')->values();
        Builder::macro('whereLike', function (string $attribute, string $searchTerm) {
            return $this->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
        });
        $manual = Manuals::query()
            ->whereLike('title', $title)
            ->get();
        // $sManual = collect(["title" => $manual->title]);

        if (!$manual) {
            return response()->json([
                'status' => 404,
                'message' => 'Manual not found',
                'manual' => $manual,
                'title' => $title
            ]);
        }

        return response()->json([
            'status' => 200,
            'manual' => $manual,
            'title' => $title
        ], 200);
    }

    //manuals uploaded by each users
    public function manualOfUser(Request $request)
    {
        $user_id = $request->user()->id;
        $role = $request->user();
        if ($role->role != 'user') {
            return response()->json([
                'status' => 401,
                'message' => 'You are not authorized for this route',
            ]);
        }

        $manual = Manuals::orderBy("created_at", "desc")->join('users', 'users.id', '=', 'manuals.user_id')->get(['users.name as uploaded_by', 'manuals.*'])->where('user_id', $user_id)->makeHidden(['user_id'])->values();
        // Manuals::all()->where('user_id', $request->user_id)->values();
        $user_data = user::where('id', $user_id)->first();
        // if ($manual->isEmpty()) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'You have not uploaded any manuals',
        //         'manual' => $manual,
        //     ], 404);
        // }

        return response()->json([
            'status' => 200,
            'id' => $user_id,
            'name' => $user_data->name,
            'email' => $user_data->email,
            'role' => $user_data->role,
            'manuals' => $manual,
        ], 200);

    }

    //pending manuals
    public function pendingManuals(Request $request)
    {
        $role = $request->user();
        if ($role->role != 'admin') {
            return response()->json([
                'status' => 401,
                'message' => 'You are not authorized for this route',
            ]);
        }
        if ($request->status) {
            $manual = Manuals::find($request->id);
            $manual->status = $request->status;
            $manual->save();
            // $updatedManual = $manual->update(['status', $request->status]);
            return response()->json([
                'status' => 204,
                'upadated_manual' => $manual,
            ]);
        }
        $manuals = Manuals::join('users', 'users.id', '=', 'manuals.user_id')->get(['users.name as uploaded_by', 'manuals.*'])->where('status', 'pending')->makeHidden('user_id')->values();
        return response()->json([
            'status' => 200,
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
