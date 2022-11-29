<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\phone;
use App\Models\phone_type;
use App\Models\account_user;
use Validator;
use Auth;

class PhoneController extends Controller
{

    public function index(Request $request)
    {
        $account_user = account_user::where('user_id',Auth::user()->id)
            ->first(['account_id','user_id']);
        $phones = phone::where('account_id',$account_user->account_id)
            ->get();

        return response()->json([
            'statut' => 1,
            'phones' => $phones,
        ]);
    }

    public function create(Request $request)
    {
        $account_user = account_user::where('user_id',Auth::user()->id)
            ->first(['account_id','user_id']);

        $phone_types = phone_type::where('account_id',$account_user->account_id)
            ->get();

            return response()->json([
            'statut' => 1,
            'phone_types' => $phone_types,
        ]);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'phone_type_id' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };

        $account_user = account_user::where('user_id',Auth::user()->id)
            ->first(['account_id','user_id']);

        $phone_only = collect($request->only('phone', 'phone_type_id'))
            ->put('account_id',$account_user->account_id)
            ->all();

        $phone = phone::create($phone_only);    
        return response()->json([
            'statut' => 1,
            'phone' => $phone,
        ]);
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $account_user = account_user::where('user_id',Auth::user()->id)
            ->first(['account_id','user_id']);
        $phone = phone::where('account_id',$account_user->account_id)
            ->where('id',$id)->get();

        $phone_types = phone_type::where('account_id',$account_user->account_id)
            ->get();

            return response()->json([
            'statut' => 1,
            'phone' => $phone,
            'phone_types' => $phone_types,
        ]);
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'phone_type_id' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };

        $account_user = account_user::where('user_id',Auth::user()->id)
        ->first(['account_id','user_id']);
    
        $offer_only = collect($request->all())
            ->only('phone', 'phone_type_id')
            ->put('account_id', $account_user->account_id)
            ->all();
            
        $offer = phone::find($id)
            ->update($offer_only);

        $offer_updated = phone::find($id);

        return response()->json([
            'statut' => 1,
            'phone' => $offer_updated,
        ]);
    }


    public function destroy($id)
    {
        $phone_b =  phone::find($id);
        $phone = phone::find($id)->delete();
        return response()->json([
            'statut' => 1,
            'phone' => $phone_b,
        ]);
    }
}


