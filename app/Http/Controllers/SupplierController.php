<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\supplier;
use App\Models\adresse;
use App\Models\account;
use App\Models\phone_type;
use App\Models\phone;
use App\Models\city;

class SupplierController extends Controller
{

    public function index(Request $request)
    {
        $suppliers = supplier::get();

        return response()->json([
            'suppliers '=>$suppliers,
        ]);
    }

    public function create(Request $request)
    {
        $cities = city::get();
        $phone_types = phone_type::get();

        return response()->json([
            'statut' => 1,
            'cities' => $cities,
            'phone_types' => $phone_types,
        ]);
    }

    public function store(Request $request)
    {
        request()->validate([
            'title' => 'required',
            'adresse' => 'required',
            'phone' => 'required',
            'account_id' => 'required',
            'photo' => '',
            'photo_dir' => '',
            'statut' => '',
            'user_id' => 'required',
            'city_id' => 'required',
            'phone_type_id' => 'required'

        ]);
        $adresse = adresse::create([
            'adresse' => $request['adresse'],
            'city_id' => intval($request['city_id'])
        ]);
        $phone = phone::create([
            'phone' => $request['phone'],
            'phone_type_id' => intval($request['phone_type_id'])
        ]);
        $supplier = supplier::create([
            'title' => $request['title'],
            'phone_id' => $phone['id'],
            'adresse_id' => $adresse['id'],
            'account_id' => intval($request['account_id']),
            'photo' => $request['photo'],
            'photo_dir' => $request['photo_dir'],
            'statut' => intval($request['statut']),
        ]);
    
        return response()->json([
            'statut' => 1,
            'supplier' => $supplier 
        ]);
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $supplier = supplier::find($id);

        $cities = city::get();
        $phone_types = phone_type::get();

        return response()->json([
            'statut' => 1,
            'cities' => $cities,
            'phone_types' => $phone_types,
            'supplier' => $supplier
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'title' => 'required',
            'phone' => 'required',
            'adresse' => 'required',
            'photo' => 'required',
            'photo_dir' => 'required',
            'statut' => 'required',
        ]);
        $supplier = supplier::find($id);
        $phone = phone::find($supplier['phone_id']);
        $adresse = adresse::find($supplier['adresse_id']);

        $supplier->title = $request->input('title');
        $phone->phone = $request->input('phone');
        $adresse->adresse = $request->input('adresse');
        $supplier->photo = $request->input('photo');
        $supplier->photo_dir = $request->input('photo_dir');
        $supplier->statut = $request->input('statut');
        $supplier->save();
        $phone->save();
        $adresse->save();

        return response()->json([
            'statut' => 1,
            'phone' => $phone,
            'adresse' => $adresse,
            'supplier' => $supplier,

        ]);
    }


    public function destroy($id)
    {
        $supplier_b =  supplier::find($id);
        $supplier = supplier::find($id)->delete();
        return response()->json([
            'statut' => 'deleted successfuly',
            'supplier' => $supplier_b,
        ]);
    }
}
