<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\attribute;
use App\Models\account;
use App\Models\User;
use App\Models\types_attribute;
use App\Models\account_user;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AttributesController extends Controller
{

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'pagination' => 'required',
            'order_by' => '',
            'serach_by' => '',
            'filter_by_columns' => '',
            
        ]);
        
        if($validator->fails()){
            return response()->json([
                'Validation Errors' => $validator->errors()
            ]);
        }
        $account_user = User::find(Auth::user()->id)->account_user->first();
        $accounts_users = account::find($account_user->account_id)->account_user->pluck('id')->toArray();
        $attributes = types_attribute::whereIn('account_user_id',$accounts_users)
            ->with('attributes')->get();

        return response()->json([
            'statut ' => 1,
            'data' => $attributes,
        ]);
    }

    public function create(Request $request)
    {
        $account_user = User::find(Auth::user()->id)->account_user->first();
        $accounts_users = account::find($account_user->account_id)->account_user->pluck('id')->toArray();
        $attributes = types_attribute::whereIn('account_user_id',$accounts_users)->paginate(20);

        return response()->json([
            'statut ' => 1,
            'data' => $attributes
        ]);
    }

    public function store(Request $request)
    {
        $account_user = User::find(Auth::user()->id)->account_user->first();
        $accounts_users = account::find($account_user->account_id)->account_user->pluck('id')->toArray();
        $validator = Validator::make($request->all(), [
            'title' => Rule::unique('attributes','title')->where(fn ($query) => $query->whereIn('account_user_id', $accounts_users)),
            'statut' => 'required',
            'types_attribute_id' => Rule::exists('types_attributes','id')->where(fn ($query) => $query->whereIn('account_user_id', $accounts_users)),
        ]);

        if($validator->fails()){
            return response()->json([
                'Validation Errors' => $validator->errors()
            ]);
        }
        $attribute = account_user::find($account_user->id)
            ->attributes()->create($request->all());
    
        return response()->json([
            'statut' => 1,
            'attribute ' => $attribute,
        ]);
    }


    public function show($id)
    {
        $account_user = User::find(Auth::user()->id)->account_user->first();
        $accounts_users = account::find($account_user->account_id)->account_user->pluck('id')->toArray();
        $validator = Validator::make(['id'=>$id], [
            'id' => Rule::exists('types_attributes','id')->where(fn ($query) => $query->whereIn('account_user_id', $accounts_users)),
        ]);

        if($validator->fails()){
            return response()->json([
                'Validation Errors' => $validator->errors()
            ]);
        }
        $attribute = attribute::where('types_attribute_id',$id)
            ->whereIn('account_user_id',$accounts_users)
            ->get();

        return response()->json([
            'statut' => 1,
            'attributes' => $attribute
        ]);
    }

    public function edit($id)
    {
        $account_user = User::find(Auth::user()->id)->account_user->first();
        $accounts_users = account::find($account_user->account_id)->account_user->pluck('id')->toArray();
        $validator = Validator::make(['id'=>$id], [
            'id' => Rule::exists('attributes','id')->where(fn ($query) => $query->whereIn('account_user_id', $accounts_users)),
        ]);

        if($validator->fails()){
            return response()->json([
                'Validation Errors' => $validator->errors()
            ]);
        }
        $attribute = attribute::find($id);

        return response()->json([
            'statut' => 1,
            'attribute' => $attribute
        ]);
    }


    public function update(Request $request, $id)
    {
        $account_user = User::find(Auth::user()->id)->account_user->first();
        $accounts_users = account::find($account_user->account_id)->account_user->pluck('id')->toArray();
        $validator = Validator::make($request->all(), [
            'title' => Rule::unique('attributes','title')->where(fn ($query) => $query->whereIn('account_user_id', $accounts_users)),
            'statut' => 'required',
            'types_attribute_id' => Rule::exists('types_attributes','id')->where(fn ($query) => $query->whereIn('account_user_id', $accounts_users)),
        ]);

        if($validator->fails()){
            return response()->json([
                'Validation Errors' => $validator->errors()
            ]);
        }

        $attribute = attribute::find($id)->update($request->all());
        $attribute_updated = attribute::find($id);
        return response()->json([
            'statut' => 1,
            'attribute' => $attribute_updated,
        ]);
    }


    public function destroy($id)
    {
        // $attribute = attribute::where('id',$id)->delete();
        // return response()->json([
        //     'statut' => 'deleted successfuly',
        //     'attribute deleted ' => $attribute,
        // ]);
    }
}
