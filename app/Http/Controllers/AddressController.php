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
        $account = User::find(Auth::user()->id)->accounts->first();
        if($local == 0){
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'account_city_id' => 'required',
                'account_city_id' => 'exists:account_city,id,account_id,'.$account->id,

            ]);

            if($validator->fails()){
                return response()->json([
                    'Validation Error', $validator->errors()
                ]);       
            };
        }

        $account_city = account_city::find($request->account_city_id);
        $address = $account_city->has_addresses()->create($request->all());
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


    public static function update(Request $request, $id,$local = 0)
    {
        $account = User::find(Auth::user()->id)->accounts->first();
        if($local == 0){
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'account_city_id'=>'required|exists:account_city,id,account_id,'.$account->id,
            ]);

            if($validator->fails()){
                return response()->json([
                    'Validation Error', $validator->errors()
                ]);       
            };
        }

        $address = address::find($id);
        $address->update($request->only('address', 'account_city_id'));
        $address_updated = address::find($id);
        if($local == 1)
            return $address_updated;

        return response()->json([
            'statut' => 1,
            'data' => $address_updated,
        ]);
    }


    public function destroy($id)
    {
        $address_b =  address::find($id);
        $address = address::find($id)->delete();
        return response()->json([
            'statut' => 1,
            'address' => $address_b,
        ]);
    }
}



