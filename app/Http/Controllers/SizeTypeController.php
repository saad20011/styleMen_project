<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\type_size;
use App\Models\User;

class SizeTypeController extends Controller
{

    
    public function index(Request $request)
    {
        $type_sizes = type_size::get();

        return response()->json([
            'statut' => 1,
            'type_size' => $type_sizes,
        ]);
    }

    public function create(Request $request)
    {

    }

    public function store(Request $request)
    {
        request()->validate([
            'name' => 'required',
            'description' => 'required',
            'account_id' => 'required',
            'user_id' => 'required',
        ]);
    
        $type_size = type_size::create($request->all());
    
        return response()->json([
            'statut' => 'type_size created successfuly',
            'type_size' => $type_size,
        ]);
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'description' => 'required',
            'account_id' => 'required',
            'user_id' => 'required',
        ]);
        $type_size = type_size::find($id);
        $type_size->name = $request->input('name');
        $type_size->description = $request->input('description');
        $type_size->account_id = $request->input('account_id');
        $type_size->user_id = $request->input('user_id');
        $type_size->save();
        return response()->json([
            'statut' => 1,
            'type_size' => $type_size,
        ]);
    }


    public function destroy($id)
    {
        $type_size_b =  type_size::find($id);
        $type_size = type_size::find($id)->delete();
        return response()->json([
            'statut' => 'deleted successfuly',
            'type_size' => $type_size_b,
        ]);
    }
}
