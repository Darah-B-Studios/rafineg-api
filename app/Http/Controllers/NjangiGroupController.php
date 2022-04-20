<?php

namespace App\Http\Controllers;

use App\Http\Requests\NjangiGroupRequest;
use App\Http\Resources\NjangiGroupResource;
use App\Models\NjangiGroup;
use Illuminate\Http\Request;

class NjangiGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            "success" => true,
            "data" => NjangiGroupResource::collection(NjangiGroup::all())
        ]);
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
    public function store(NjangiGroupRequest $request)
    {
        $data = $request->validated();
        $njangiGroup = NjangiGroup::create($data);
        if ($njangiGroup) {
            return response()->json([
                "success" => true,
                "message" => "New group created successfully",
                "data" => new NjangiGroupResource($njangiGroup)
            ]);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Could not create group because an success occured",
                "data" => null
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\NjangiGroup  $njangiGroup
     * @return \Illuminate\Http\Response
     */
    public function show(NjangiGroup $njangiGroup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\NjangiGroup  $njangiGroup
     * @return \Illuminate\Http\Response
     */
    public function edit(NjangiGroup $njangiGroup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\NjangiGroup  $njangiGroup
     * @return \Illuminate\Http\Response
     */
    public function update(NjangiGroupRequest $request, NjangiGroup $njangiGroup)
    {

        $data = $request->validated();
        $njangiGroup->update($data);
        return response()->json([
            "success" => true,
            "message" => "Njangi group info updated successfully",
            "data" => new NjangiGroupResource($njangiGroup)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NjangiGroup  $njangiGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(NjangiGroup $njangiGroup)
    {
        $isDeleted = $njangiGroup->delete();
        if ($isDeleted) {
            return response()->json([
                "success" => true,
                "message" => "Njangi group deleted successfully",
                "data" => null
            ]);
        }

        return response()->json([
            "success" => false,
            "message" => "Njangi group could not be deleted",
            "data" => null
        ]);
    }
}