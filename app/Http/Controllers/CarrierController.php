<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\carrier;
use App\Models\account;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class CarrierController extends Controller
{

    public function index(Request $request)
    {
        $account = User::find(Auth::user()->id)->accounts->first();
        $carriers = account::find($account->id)->carriers;

        return response()->json([
            'statut' => 1,
            'data' => $carriers,
        ]);
    }

    public function create(Request $request)
    {
        //
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required',
            'email' => 'required',
            'trackinglink' => 'required',
            'autocode' => 'required',
            'comment' => 'required',
            'statut'=>'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error'=> $validator->errors()
            ]);
        };
        $account = User::find(Auth::user()->id)->accounts->first();
        $carrier_acc = collect($request->all())->put('account_id', $account->id);
        $carrier = account::find($account->id)->has_carriers()->create($request->all());
        $account->carriers()->attach([$carrier->id=>[
            'autocode'=> 1,
            'statut'=>1
        ]]);
        return response()->json([
            'statut' => 1,
            'data' => $carrier,
        ]);
    }
    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $account = User::find(Auth::user()->id)->accounts->first();
        $carrier = carrier::find($id);
        return response()->json([
            'statut' => 1,
            'data' => $carrier
        ]);
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required',
            'email' => 'required',
            'trackinglink' => 'required',
            'autocode' => 'required',
            'comment' => 'required',
            'statut' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Errors' => $validator->errors()
            ]);
        }
        $account = User::find(Auth::user()->id)->accounts->first();
        $carrier = carrier::find($id)
            ->where(['id' => $id, 'account_id' =>$account->id])
            ->update($request->all());
        $carrier_updated = carrier::find($id);
        return response()->json([
            'statut' => 1,
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
