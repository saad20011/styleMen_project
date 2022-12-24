<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\offer;
use App\Models\User;
use App\Models\account;
use App\Models\account_user;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OfferController extends Controller
{

    public function index(Request $request)
    {
        
        $account = User::find(Auth::user()->id)->accounts->first();
        $offers = account::find($account->id)->offers;
        return response()->json([
            'statut' => 1,
            'offer' => $offers,
        ]);
    }

    public function create(Request $request)
    {
        $account = User::find(Auth::user()->id)->accounts->first();
        $brands = account::find($account->id)->brands;

        return response()->json([
            'statut' => 1,
            'data' => $brands,
        ]);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required|', //unique:offers
            'price' => 'required',
            'shipping_price' => 'required',
            'statut' => 'required',
            'brands' => 'required|array|min:1',
            'brands.*'=> 'required|exists:brands,id'
        ]);

        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };
        $account = User::find(Auth::user()->id)->accounts->first();
        $mother_offer = account::find($account->id)->offers()
                ->create($request->all());
        $mother_offer->offers = collect($request->brands)->map(function($brand_id) use($account, $mother_offer){
            $offer = account::find($account->id)->offers()
                ->create(['offer_id'=>$mother_offer->id, 'brand_id'=>$brand_id]);
            return $offer;
        });

        return response()->json([
            'statut' => 1,
            'data' => $mother_offer,
        ]);


    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $account = User::find(Auth::user()->id)->accounts->first();
        $brands = account::find($account->id)->brands;
        
        return response()->json([
            'statut' => 1,
            'data' => $brands,
        ]);
    }


    public function update(Request $request, $id)
    {
        $account = User::find(Auth::user()->id)->accounts->first();
        $validator = Validator::make($request->all(), [
            'title' => 'required|', //unique:offers
            'price' => 'required',
            'shipping_price' => 'required',
            'offer_id' => 'required|exists:offers,id,account_id,'.$account->id,
            'brands' => 'array',
            'brands.*'=> 'exists:brands,id,account_id,'.$account->id,
        ]);

        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        }
        $account = User::find(Auth::user()->id)->accounts->first();
        $offer = offer::find($request->offer_id)->update($request->all());
        $offer_updated = offer::find($request->offer_id);
        $this->update_brand_offers($account->id,$request->offer_id,$request->brands);

        return response()->json([
            'statut' => 1,
            'data' => $offer_updated,
        ]);
    }


    public function destroy($id)
    {
        $offer_b =  offer::find($id);
        $offer = offer::find($id)->delete();
        return response()->json([
            'statut' => 'deleted successfuly',
            'offer' => $offer_b,
        ]);
    }


    public static function update_brand_offers($account_id, $offer_id, $brands)
    {
        // change offers
        $offers = account::find($account_id)->offers()->where(['offer_id'=> $offer_id, 'account_id'=> $account_id])->get();
        foreach($offers as $offer ){
            if(in_array($offer->brand_id, $brands )==true ){
                if( $offer->statut==0)
                    offer::find($offer->id)->update(['statut'=>1]);
            
            }else{ 
                offer::find($offer->id)->update(['statut'=>0]);
            }
        }
        foreach($brands as $brand ){
            $exist = collect($offers)->contains('brand_id',$brand);
            if($exist == false ){

                $offer = account::find($account_id)->offers()
                ->create(['offer_id'=>$offer_id, 'brand_id'=>$brand]);
            }
        }
        return true;
    }
}

