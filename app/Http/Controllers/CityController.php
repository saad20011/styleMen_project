<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use App\Models\city;
use App\Models\User;
use App\Models\account;
use App\Models\region;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CityController extends Controller
{

    public function index(Request $request)
    {
        $cities = city::get();

        return response()->json([
            'statut ' => 1,
            'data' => $cities,
        ]);
    }

    public function create(Request $request)
    {
        $regions = region::get();

        return response()->json([
            'statut ' => 1,
            'data ' => $regions
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            '*.title' => 'required',
            '*.statut' => 'required',
            '*.region_id' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };
        $cities = collect($request->all())->map(function ($city) {
            $city_only=collect($city)->only('title','statut','region_id');
            $city = city::create($city_only->all());
            return $city;
        });

        return response()->json([
            'statut' => 1,
            'data' =>  $cities,
        ]);
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $city = city::find($id);

        return response()->json([
            'statut' => 1,
            'data' => $city
        ]);
    }

    public function edit_all_cities(Request $request)
    {
        $cities = city::with('regions')->get();
        $regions = region::get();

        return response()->json([
            'statut' => 1,
            'cities' => $cities,
            'regions'=>$regions,

        ]);
    }

    public function update(Request $request, $id,$local=false)
    {
        $validator = Validator::make($request->all(), [
            'id'=>'required',
            'title' => 'required',
            'statut' => 'required',
            'region_id' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };

        $city_col = collect($request->all())->only('title','statut','region_id')->all();
        $city = city::find($request->id)->update($city_col);
        $city_updated = city::find($request->id);
        if(!$local){
            return response()->json([
                'statut' => 'your city is updated successfuly',
                'city' => $city_updated,
            ]);
        }else{
        return $city_updated;
    }
    }

    public function update_all_cities(Request $request)
    {
        $validator = Validator::make($request->all(), [
            '*.id' => 'required',
            '*.title' => 'required',
            '*.statut' => 'required',
            '*.region_id' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };
        foreach($request->all() as $city){
            $city_re = new Request($city);
            $cities = $this->update($city_re,$local=true);
        }

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
