<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\charge_type;
class ChargeTypeController extends Controller
{

    public function index(Request $request)
    {
        $charge_types = charge_type::get();

        return response()->json([
            'statut' => 1,
            'charge_type'=>$charge_types,
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
    
        $charge_type = charge_type::create($request->all());
    
        return response()->json([
            'statut' => 1,
            'product' => $charge_type,
        ]);
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $charge_type = charge_type::find($id);
        return response()->json([
            'statut' => 1,
            'charge_type' => $charge_type,
        ]);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'statut' => 'required'
        ]);
        $charge_type = charge_type::find($id);
        $charge_type->title = $request->input('title');
        $charge_type->statut = $request->input('statut');
        $charge_type->save();
        return response()->json([
            'statut' => 1,
            'charge_type' => $charge_type,
        ]);
    }


    public function destroy($id)
    {
        $charge_type = charge_type::where('id',$id)->delete();
        return response()->json([
            'statut' => 1,
            'role' => $charge_type,
        ]);
    }
}
