<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\phone;
use App\Models\phone_type;

class PhoneController extends Controller
{

    public function index(Request $request)
    {
        $phones = phone::get();

        return response()->json([
            'statut' => 1,
            'phones' => $phones,
        ]);
    }

    public function create(Request $request)
    {
        $phone_types = phone_type::get();
        return response()->json([
            'statut' => 1,
            'phone_types' => $phone_types,
        ]);
    }

    public function store(Request $request)
    {
        request()->validate([
            'phone' => 'required',
            'phone_type_id' => 'required',
        ]);
    
        $phone = phone::create($request->all());
    
        return response()->json([
            'statut' => 1,
            'phone' => $phone,
        ]);
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $phone = phone_type::find($id);
        return response()->json([
            'statut' => 1,
            'phone' => $phone,
        ]);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'phone' => 'required',
            'phone_type_id' => 'required',
        ]);
        $phone = phone::find($id);
        $phone->phone = $request->input('phone');
        $phone->phone_type_id = $request->input('phone_type_id');
        $phone->save();
        return response()->json([
            'statut' => 1,
            'phone' => $phone,
        ]);
    }


    public function destroy($id)
    {
        $phone_b =  phone::find($id);
        $phone = phone::find($id)->delete();
        return response()->json([
            'statut' => 1,
            'phone' => $phone_b,
        ]);
    }
}


