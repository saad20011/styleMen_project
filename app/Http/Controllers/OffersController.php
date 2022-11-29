<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\offer;
use App\Models\User;
use App\Models\account_user;
use Validator;
use Auth;

class OffersController extends Controller
{

    public function index(Request $request)
    {
        
        $account_user = account_user::where('user_id',Auth::user()->id)
            ->first(['account_id','user_id']);
        $offers = offer::where('account_id',$account_user->account_id)
            ->get();

        return response()->json([
            'statut' => 1,
            'offer' => $offers,
        ]);
    }

    public function create(Request $request)
    {

    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'price' => 'required',
            'shipping_price' => 'required',
            'statut' => 'required',
            'brand_id' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };

        $account_user = account_user::where('user_id',Auth::user()->id)
            ->first(['account_id','user_id']);

        $offer_only = collect($request->only('title', 'price', 'shipping_price', 'statut', 'brand_id', 'description'))
            ->put('account_id',$account_user->account_id)
            ->all();

        $offer = offer::create($offer_only);
    
        return response()->json([
            'statut' => 1,
            'offer' => $offer,
        ]);
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'price' => 'required',
            'shipping_price' => 'required',
            'statut' => 'required',
            'brand_id' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };
        $account_user = account_user::where('user_id',Auth::user()->id)
        ->first(['account_id','user_id']);
    
        $offer_only = collect($request->all())
            ->only('title', 'price', 'shipping_price', 'statut', 'brand_id')
            ->put('account_id', $account_user->account_id)
            ->all();
            
        $offer = offer::find($id)
            ->update($offer_only);
        $offer_updated = offer::find($id);

        return response()->json([
            'statut' => 1,
            'offer' => $offer_updated,
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
}
