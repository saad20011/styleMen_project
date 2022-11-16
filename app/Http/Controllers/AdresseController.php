<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\adresse;
use App\Models\city;

class AdresseController extends Controller
{

    public function index(Request $request)
    {
        $adresses = adresse::get();

        return response()->json([
            'adresses '=>$adresses,
        ]);
    }

    public function create(Request $request)
    {
        $cities = city::get();

        return response()->json([
            'statut' => 1,
            'cities ' => $cities,
        ]);

    }

    public function store(Request $request)
    {
        request()->validate([
            'adresse' => 'required',
            'city_id' => 'required',
        ]);
    
        $adresse = adresse::create($request->all());
    
        return response()->json([
            'statut' => 1,
            'adresse' => $adresse,
        ]);
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $adresse = city::find($id);

        return response()->json([
            'statut' => 1,
            'adresse ' => $adresse
        ]);

    }

    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'adresse' => 'required',
            'city_id' => 'required',
        ]);
        $adresse = adresse::find($id);
        $adresse->adresse = $request->input('adresse');
        $adresse->city_id = $request->input('city_id');
        $adresse->save();
        return response()->json([
            'statut' => 1,
            'adresse' => $adresse,
        ]);
    }


    public function destroy($id)
    {
        $adresse_b =  adresse::find($id);
        $adresse = adresse::find($id)->delete();
        return response()->json([
            'statut' => 1,
            'adresse' => $adresse_b,
        ]);
    }
}
