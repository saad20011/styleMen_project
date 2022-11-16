<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use App\Models\city;
use App\Models\region;
use App\Models\accounts_city;
use Validator;

class CityController extends Controller
{

    public function index(Request $request)
    {
        $cities = city::get();

        return response()->json([
            'statut ' => 1,
            'cities ' => $cities,
        ]);
    }

    public function create(Request $request)
    {
        $cities = region::get();

        return response()->json([
            'statut ' => 1,
            'cities ' => $cities
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            '*.title' => 'required',
            '*.statut' => 'required',
            '*.region_id' => 'required',
            '*.preferred' => 'required',
            '*.account_id' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };
        $cities = collect($request->all())->map(function ($city) {
            $city_only = collect($city)->only('title','statut','region_id','preferred')->toArray();
            $city_row = city::create($city);
            $accounts_city = accounts_city::create([
                'city_id'=>$city_row['id'],
                'account_id'=>$city['account_id'],
                'preferred'=>$city['preferred'],
                'statut'=>$city['statut'],
            ]);
            return ['city' => $city_row, 'account_city' => $accounts_city];
        });



        return response()->json([
            'statut' => 'product created successfuly',
            'city ' =>  $cities,
        ]);
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $city = city::where('id',$id)->first();

        return response()->json([
            'statut' => 1,
            'city' => $city
        ]);
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
            '*.title' => 'required',
            '*.statut' => 'required',
            '*.region_id' => 'required',
            '*.preferred' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };
        $city = city::find($id);
        $city->title = $request->input('title');
        $city->statut = $request->input('statut');
        $city->region_id = $request->input('region_id');
        $city->preferred = $request->input('preferred');

        $city->save();
        return response()->json([
            'statut' => 'your city is updated successfuly',
            'city' => $city,
        ]);
    }

    public function update_all_cities(Request $request)
    {
        $validator = Validator::make($request->all(), [
            '*.id' => 'required',
            '*.title' => 'required',
            '*.statut' => 'required',
            '*.region_id' => 'required',
            '*.preferred' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };
        $cities = collect($request->all())->map(function ($city) {
            $city_row = city::find($city['id']);
            $city_row->title = $city['title'];
            $city_row->statut = $city['statut'];
            $city_row->region_id = $city['region_id'];
            $city_row->preferred = $city['preferred'];
            $city_row->save();
            return $city_row;
        });

        return response()->json([
            'statut' => 1,
            'city' => $cities,
        ]);
    }

    public function destroy($id)
    {
        $city = city::where('id',$id)->delete();
        return response()->json([
            'statut' => 'deleted successfuly',
            'city deleted ' => $city,
        ]);
    }
}
