<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\adresse;
use App\Models\city;
use Validator;
class AdresseController extends Controller
{

    public function index(Request $request)
    {
        $adresses = adresse::get();
        //commentaires
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
        $adresse_only = collect($request->all())->only('adresse','city_id')->all();
        $adresse = adresse::create($adresse_only);
    
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

        $validator = Validator::make($request->all(), [
            'adresse' => 'required',
            'city_id' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };
        $adresse_col = collect($request->all())->only('adresse','city_id')->all();
        $adresse = adresse::find($id)->update($adresse_col);
        $adresse_updated = adresse::find($id);
        return response()->json([
            'statut' => 1,
            'adresse' => $adresse_updated,
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
