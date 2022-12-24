<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\address;
use App\Models\User;
use App\Models\account;
use App\Models\account_city;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{

    public function index(Request $request)
    {
        $account = User::find(Auth::user()->id)->accounts->first();
        $account_citys = account::find($account->id)->account_city;
        $addresses = [];
        foreach( $account_citys as $account_city){
            array_push(
                $addresses,
                address::where('account_city_id', $account_city->id)->get()
            );
        }

        return response()->json([
            'statut' => 12,
            'addresses' => collect($addresses)->collapse(),
        ]);
    }

    public function create(Request $request, $local =0)
    {
        $account = User::find(Auth::user()->id)->accounts->first();
        $cities = account::find($account->id)->cities;

        if($local = 1){
            return $cities;
        }
        return response()->json([
            'statut' => 1,
            'data' => $cities,
        ]);
    }

    public static function store(Request $request, $local=0, $table=null)
    {
        if($local == 0){
            $validator = Validator::make($request->all(), [
                'address' => 'required',
                'account_city_id' => 'required',
            ]);

            if($validator->fails()){
                return response()->json([
                    'Validation Error', $validator->errors()
                ]);       
            };
        }

        $account = User::find(Auth::user()->id)->accounts->first();
        $account_city = account_city::find($request->account_city_id);
        $address = $account_city->has_addresses()->create([
            'address' => $request->address,
            'account_city_id' => $request->account_city_id
        ]);
        if($local ==1 )
            $table->addresses()->attach($address->id);
            return $address;
        
        return response()->json([
            'statut' => 1,
            'address' => $address,
        ]);
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $account = User::find(Auth::user()->id)->accounts->first();
        $cities = account::find($account->id)->cities;
        $address = address::find($id);
        if(!$address){
            return response()->json([
                'statut' => 1,
                'data' => 'not found'
            ]);
        }
        $account_city = account_city::find($address->account_city_id);
        if($account_city->account_id != $account->id){
            return response()->json([
                'statut' => 1,
                'data' => 'not found'
            ]);
        }
        return response()->json([
            'statut' => 1,
            'address' => $address,
            'cities' => $cities
        ]);
    }


    public static function update(Request $request, $id, $local = 0)
    {
        //si la mise a jour est locale
        if($local == 0){
            $validator = Validator::make($request->all(), [
                'address' => 'required',
                'account_city_id' => 'required',
            ]);

            if($validator->fails()){
                return response()->json([
                    'Validation Error', $validator->errors()
                ]);       
            };
        }
        //verifier si l'adresse est disponible
        $address = address::find($id);
        if(!$address){
            return response()->json([
                'statut' => 1,
                'data' => 'not found'
            ]);
        }
        //recuperer l'account pour comparer l'id du account avec l'account_id du account_city
        $account = User::find(Auth::user()->id)->accounts->first();
        //recuper l'account_city du account_city recu dans la request
        $account_city_request = account_city::find($request->account_city_id);
        if($account_city_request->account_id != $account->id){
            return response()->json([
                'statut' => 1,
                'data' => 'not found'
            ]);
        }
        //mis a jour de l'adresse
        $address=address::find($id);
        $updated=$address->update(['address' =>  $request->input('address'),'account_city_id' =>  $request->input('account_city_id')]);
        if($updated){
            //recuperation de l'adresse pour l'envoi
            $address=address::find($id);
            if($local == 1)
                return $address;

            return response()->json([
                'statut' => 1,
                'data' => $address,
            ]);
        }
        
        return response()->json([
            'statut' => 0,
            'data' => 'update address error',
        ]);
    }


    public function destroy($id)
    {
        //l'adresse makhassoch yqder ymssa7ha walakine yqder y modifiehha bach tbqa 3endna dima une adresse nkhadmo biha
        /*$address_b =  address::find($id);
        $address = address::find($id)->delete();
        return response()->json([
            'statut' => 1,
            'address' => $address_b,
        ]);*/
    }
}


