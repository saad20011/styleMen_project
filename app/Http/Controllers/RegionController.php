<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Region;

class RegionController extends Controller
{
    public function index(Request $request)
    {
        $regions = region::get();

        return response()->json([
            'you'=>$regions,
        ]);
    }

    public function create(Request $request)
    {

    }

    public function store(Request $request)
    {
        request()->validate([
            'title' => 'required',
            'statut' => 'required',
        ]);
    
        $region = region::create($request->all());
    
        return response()->json([
            'statut' => 'product created successfuly',
            'region' => $region,
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
            'region' => $region,
        ]);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'statut' => 'required'
        ]);
        $region = region::find($id);
        $region->title = $request->input('title');
        $region->statut = $request->input('statut');
        $region->save();
        return response()->json([
            'statut' => 'your phone type is updated successfuly',
            'region' => $region,
        ]);
    }


    public function destroy($id)
    {
        $region = region::where('id',$id)->delete();
        return response()->json([
            'statut' => 'deleted successfuly',
            'role' => $region,
        ]);
    }
}
