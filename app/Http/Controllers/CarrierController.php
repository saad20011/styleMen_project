<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\carrier;
use App\Models\User;
use App\Models\phone;
use App\Models\adresse;
use App\Models\accounts_carrier;
use App\Models\accounts_city;
use App\Models\accounts_carriers_city;
use Validator;

class CarrierController extends Controller
{

    public function index(Request $request)
    {
        $carriers = carrier::where('account_id',$request->$account_id)->get();

        return response()->json([
            'statut' => 1,
            'carriers'=>$carriers,
        ]);
    }

    public function create(Request $request)
    {
        $phones = phone::get();
        $adresses = adresse::get();
        $users = User::get();
        return response()->json([
            'statut' => 1,
            'phones' => $phones,
            'adresses' => $adresses,
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required',
            'phone_id' => 'required',
            'adresse_id' => 'required',
            'email' => 'required',
            'trackinglink' => 'required',
            'autocode' => 'required',
            'photo' => 'required',
            'photo_dir' => 'required',
            'comment' => 'required',
            'statut' => 'required',
            'user_id' => 'required',
            'autocode'=>'required',
            'statut'=>'required',
            'all_cities'=>'required',
            'account_id'=>'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error'=> $validator->errors()
            ]);
        };
        $carrier = carrier::create($request->all());
        $account_carrier = accounts_carrier::create([
            'carrier_id' => $carrier['id'],
            'account_id'=> $request['account_id'],
            'autocode'=>$request['autocode'],
            'statut'=>$request['statut'],
        ]); 

        return response()->json([
            'statut' => 'product created successfuly',
            'carrier' => $carrier,
            $account_carrier
        ]);
    }
    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $carrier = carrier::find($id);
        return response()->json([
            'statut' => 1,
            'carrier' => $carrier
        ]);
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required',
            'phone' => 'required',
            'adresse' => 'required',
            'email' => 'required',
            'trackinglink' => 'required',
            'autocode' => 'required',
            'photo' => 'required',
            'photo_dir' => 'required',
            'comment' => 'required',
            'statut' => 'required',
            'user_id' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Errors' => $validator->errors()
            ]);
        }
        $carr_col = collect($request->all())
            ->only('title','email','trackinglink','autocode','photo','photo_dir','comment','statut')
            ->toArray();
        $carrier = carrier::find($id)
            ->update($carr_col);
        $carrier_updated = carrier::find($id);
        return response()->json([
            'statut' => $carrier,
            'carrier' => $carrier_updated,
        ]);
    }


    public function destroy($id)
    {
        $carrier_b =  carrier::find($id);
        $carrier = carrier::find($id)->delete();
        return response()->json([
            'statut' => 1,
            'carrier' => $carrier_b,
        ]);
    }
}
