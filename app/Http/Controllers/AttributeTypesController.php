<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\types_attribute;
use App\Models\User;
use App\Models\account_user;
use App\Models\account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AttributeTypesController extends Controller
{

    
    public function index(Request $request)
    {
        $account_user = User::find(Auth::user()->id)->account_user->first();
        $accounts_users = account::find($account_user->account_id)->account_user->pluck('id')->toArray();
        $types_attribute = types_attribute::whereIn('account_user_id',$accounts_users)->get();

        return response()->json([
            'statut' => 1,
            'data' => $types_attribute,
        ]);
    }

    public function create(Request $request)
    {

    }

    public function store(Request $request)
    {
        $account_user = User::find(Auth::user()->id)->account_user->first();
        $accounts_users = account::find($account_user->account_id)->account_user->pluck('id')->toArray();
        $validator = Validator::make($request->all(), [
            'title' => ['required', Rule::unique('types_attributes','title')->where(fn ($query) => $query->whereIn('account_user_id', $accounts_users))],
            'description' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };
        $types_attribute = account_user::find($account_user->id)->types_attributes()->create($request->all());
    
        return response()->json([
            'statut' => 1,
            'data' => $types_attribute,
        ]);
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {

        $account_user = User::find(Auth::user()->id)->account_user->first();
        $accounts_users = account::find($account_user->account_id)->account_user->pluck('id')->toArray();
        $validator = Validator::make(['id'=>$id], [
            'id' => Rule::exists('types_attributes','id')->where(fn ($query) => $query->whereIn('account_user_id', $accounts_users)),
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };
        $types_attribute = types_attribute::find($id);

        return response()->json([
            'statut' => 1,
            'data' => $types_attribute,
        ]);
    }


    public function update(Request $request, $id)   
    {
        $account_user = User::find(Auth::user()->id)->account_user->first();
        $accounts_users = account::find($account_user->account_id)->account_user->pluck('id')->toArray();
        $validator = Validator::make(array_merge($request->all(), ['id'=>$id]), [
            'id' => Rule::exists('types_attributes','id')->where(fn ($query) => $query->whereIn('account_user_id', $accounts_users)),
            'title' => ['required', Rule::unique('types_attributes','title')->where(fn ($query) => $query->whereIn('account_user_id', $accounts_users))],
            'description' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };
        $types_attribute = types_attribute::find($id)->update($request->all());
        $types_attribute_updated = types_attribute::find($id);

        return response()->json([
            'statut' => 1,
            'data' => $types_attribute_updated,
        ]);
    }


    public function destroy($id)
    {
        // $types_attribute_b =  types_attribute::find($id);
        // $types_attribute = types_attribute::find($id)->delete();
        // return response()->json([
        //     'statut' => 'deleted successfuly',
        //     'types_attribute' => $types_attribute_b,
        // ]);
    }
}
