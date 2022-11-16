<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\phone_type;
use App\Models\User;

class PhoneTypeController extends Controller
{

    
    public function index(Request $request)
    {
        $phone_types = phone_type::get();

        return response()->json([
            'statut' => 1,
            'phone types' => $phone_types,
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
    
        $phone_type = phone_type::create($request->all());
    
        return response()->json([
            'statut' => 'phone_type created successfuly',
            'phone_type' => $phone_type,
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
            'title' => 'required',
            'statut' => 'required'
        ]);
        $phone_type = phone_type::find($id);
        $phone_type->title = $request->input('title');
        $phone_type->statut = $request->input('statut');
        $phone_type->save();
        return response()->json([
            'statut' => 'your phone type is updated successfuly',
            'phone_type' => $phone_type,
        ]);
    }


    public function destroy($id)
    {
        $phone_type_b =  phone_type::find($id);
        $phone_type = phone_type::find($id)->delete();
        return response()->json([
            'statut' => 'deleted successfuly',
            'phone_type' => $phone_type_b,
        ]);
    }
}
