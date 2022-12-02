<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\region;
use App\Models\account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RegionController extends Controller
{
    public function index(Request $request)
    {
        $regions = region::get();

        return response()->json([
            'statut'=>1,
            'data'=>$regions,
        ]);
    }

    public function create(Request $request)
    {

    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'statut' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };
        $region = region::create($request->only('title', 'statut'));
    
        return response()->json([
            'statut' => 1,
            'data' => $region,
        ]);
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $region = region::find($id);

        return response()->json([
            'statut' => 1,
            'data' => $region,
        ]);
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'statut' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };
        $region = region::find($$id)->update($request->only('title', 'statut'));
        $region = region::find($id);

        return response()->json([
            'statut' => 1,
            'data' => $region,
        ]);
    }


    public function destroy($id)
    {
        $region = region::where('id',$id)->delete();
        return response()->json([
            'statut' => 'deleted successfuly',
            'data' => $region,
        ]);
    }
}
