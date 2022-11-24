<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\phone_type;
use App\Models\User;
use App\Models\account_user;
use Auth;
use Validator;

class PhoneTypeController extends Controller
{

    
    public function index(Request $request)
    {

        $account_user = account_user::where('user_id',Auth::user()->id)
            ->first(['account_id','user_id']);
        $phone_types = phone_type::where('account_id',$account_user->account_id)
            ->get();
        
            return response()->json([
            'statut' => 1,
            'phone types' => $phone_types,
        ]);
    }

    public function create(Request $request)
    {

    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'statut' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };

        $account_user = account_user::where('user_id',Auth::user()->id)
            ->first(['account_id','user_id']);

        $phone_type_only = collect($request->only('title', 'statut'))
            ->put('account_id',$account_user->account_id)
            ->all();

        $phone_type = phone_type::create($phone_type_only);
    
        return response()->json([
            'statut' => 'phone_type created successfuly',
            'phone_type' => $phone_type,
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
        $this->validate($request, [
            'title' => 'required',
            'statut' => 'required'
        ]);
        $phone_type = phone_type::find($id);
        $phone_type->title = $request->input('title');
        $phone_type->statut = $request->input('statut');
        $phone_type->save();
        return response()->json([
            'statut' => 'your phone type is updated successfuly',
            'phone_type' => $phone_type,
        ]);
    }


    public function destroy($id)
    {
        $phone_type_b =  phone_type::find($id);
        $phone_type = phone_type::find($id)->delete();
        return response()->json([
            'statut' => 'deleted successfuly',
            'phone_type' => $phone_type_b,
        ]);
    }
}
