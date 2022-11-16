<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use App\Models\city;
use App\Models\accounts_city;
use App\Models\accounts_carriers_city;
use Validator;

class Accounts_CarriersCities extends Controller
{

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'account_carrier_id'=>'required'
        ]);
        if( $validator->fails()){
            return response()->json([
                'validation errors' => $validator->errors()
            ]);
        }
         
        $cities_carrier = DB::table('accounts_carriers_cities')
        ->join('accounts_cities', 'accounts_carriers_cities.account_city_id', '=', 'accounts_cities.id')
        ->join('cities', 'accounts_cities.city_id', '=', 'cities.id')
        ->where('account_carrier_id',$request['account_carrier_id'])
        ->select('cities.title as original_name',
                'accounts_carriers_cities.name as name',
                'accounts_carriers_cities.return',
                'accounts_carriers_cities.delivery_time',
                'accounts_carriers_cities.statut',
                'accounts_carriers_cities.id as carrier_city_id' ,
                'cities.id'
                )
        ->get();

        return response()->json([
            'statut ' => 1,
            'cities ' => $cities_carrier,
        ]);
    }

    public function create(Request $request)
    {
        $account_cities = DB::table('accounts_cities')
        ->join('cities', 'accounts_cities.city_id', '=', 'cities.id')
        ->select('accounts_cities.id as account_city_id',
                'cities.title'
        )
        ->where('accounts_cities.account_id',$request['account_id'])
        ->get();
    

        return response()->json([
            'statut ' => 1,
            'cities ' => $account_cities
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_carrier_id'=>'required',
            'account_city_id'=>'required',
            'name'=>'required',
            'price'=>'required',
            'return'=>'required',
            'delivery_time'=>'required',
            'statut'=>'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        }
        $carr_col = collect($request->all())
        ->only('account_carrier_id','account_city_id','name','price','return','delivery_time','statut')
        ->toArray();
        $accouts_carriers_city = accounts_carriers_city::create($carr_col);
    
        return response()->json([
            'statut' => 1,
            'accouts_carriers_city ' =>  $accouts_carriers_city,
        ]);
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {

    }

    public function edit_all_cities(Request $request)
    {
        $cities = accounts_city::where('account_id',$request->account_id)->get('city_id');
        $cities_acount = city::whereIn('id',$cities)->get();

        $regions = region::get();
        return response()->json([
            'statut' => 1,
            'cities_acount' => $cities_acount,

        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'carrier_city_id' => 'required',
            'name'=>'required',
            'price'=>'required',
            'return'=>'required',
            'delivery_time'=>'required',
            'statut'=>'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };
        $carr_city_col = collect($request->all())
        ->only('carrier_city_id','name','price','return','delivery_time','statut')
        ->toArray();
        $carr_city = accounts_carriers_city::find($request['carrier_city_id'])
        ->update($carr_city_col);
        $carr_city_updated = accounts_carriers_city::find($request['carrier_city_id']);

        return response()->json([
            'statut' => 1,
            'carrier_city' => $carr_city_updated ,
        ]);
    }

    public function update_all_carrier_cities(Request $request)
    {
        $validator = Validator::make($request->all(), [
            '*.carrier_city_id' => 'required',
            '*.name'=>'required',
            '*.price'=>'required',
            '*.return'=>'required',
            '*.delivery_time'=>'required',
            '*.statut'=>'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };
        $carr_cities_updated = collect($request->all())->map(function ($carrier_city) {
            $carr_city_col = collect($carrier_city)
            ->only('carrier_city_id','name','price','return','delivery_time','statut')
            ->toArray();
            $carr_city = accounts_carriers_city::find($carrier_city['carrier_city_id'])
            ->update($carr_city_col);
            $carr_city_updated = accounts_carriers_city::find($carrier_city['carrier_city_id']);
            return $carr_city_updated;
        });

        return response()->json([
            'statut' => 1,
            'carr_cities_updated' => $carr_cities_updated,
        ]);
    }

    public function destroy($id)
    {

    }
}
