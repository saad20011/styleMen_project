<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\phone;
use App\Models\User;
use App\Models\account;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PhoneController extends Controller
{

    public function index(Request $request)
    {
        $account = User::find(Auth::user()->id)->accounts->first();
        $phones = account::find($account->id)->phones;

        return response()->json([
            'statut' => 1,
            'data' => $phones,
        ]);
    }

    public function create(Request $request)
    {
        $account = User::find(Auth::user()->id)->accounts->first();
        $phone_types = account::find($account->id)->phone_types;

            return response()->json([
            'statut' => 1,
            'data' => $phone_types,
        ]);
    }

    public static function store(Request $request, $local=0, $table=null)
    {
        if($local == 0){
            $validator = Validator::make($request->all(), [
                'phone' => 'required',
                'phone_type_id' => 'required',
            ]);

            if($validator->fails()){
                return response()->json([
                    'Validation Error', $validator->errors()
                ]);       
            };
        }

        $account = User::find(Auth::user()->id)->accounts->first();
        $phone = $account->has_phones()->create([
            'phone_type_id' => $request->phone_type_id,
            'title' => $request->phone
        ]);
        if($local ==1 )
            $table->phones()->attach($phone->id);
            return $phone;
        
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
        $account = User::find(Auth::user()->id)->accounts->first();
        $phone = account::where(['account_id' => $account->id, 'id' => $id])
            ->first();

        return response()->json([
            'statut' => 1,
            'data' => $phone
        ]);
    }


    public static function update(Request $request, $local = 0, $id)
    {
        //si la mise a jour est locale
        if($local == 0){
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'phone_type_id' => 'required',
            ]);
            if($validator->fails()){
                return response()->json([
                    'Validation Error', $validator->errors()
                ]);       
            };
        }
        //recuperation de telephone pour l'envoi
        $phone=phone::find($id);
        //mis a jour de telephone
        $updated=$phone->update(['title' =>  $request->input('title')]);
        if($updated){
            if($local == 1)
                return $phone;

            return response()->json([
                'statut' => 1,
                'data' => $phone,
            ]);
        }
        return response()->json([
            'statut'=>0,
            'data'=>'update phone error'
        ]);
    }


    public function destroy($id)
    {
        //téléphone makhassnach nmass7oha 7ite ghadi ykhassna dima nemra de téléphone nkhadmo bih 
        /*$phone_b =  phone::find($id);
        $phone = phone::find($id)->delete();
        return response()->json([
            'statut' => 1,
            'phone' => $phone_b,
        ]);*/
    }
}


