<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\size;
use App\Models\account;
use App\Models\User;
use App\Models\type_size;
use App\Models\account_user;
use Validator;
use Auth;

class SizeController extends Controller
{

    public function index(Request $request)
    {
        $account_user = account_user::where('user_id',Auth::user()->id)
            ->first(['account_id','user_id']);
        $sizes = size::where('account_id',$account_user->account_id)
            ->get();

        return response()->json([
            'statut ' => 1,
            'sizes ' => $sizes,
        ]);
    }

    public function create(Request $request)
    {
        $type_sizes = type_size::get();

        return response()->json([
            'statut ' => 1,
            'type_sizes ' => $type_sizes
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|unique:size',
            'statut' => 'required',

            'photo' => 'required',
            'photo_dir' => 'required',
            'type_size_id'=>'required'
        ]);
        $account_user = account_user::where('user_id',Auth::user()->id)
        ->first(['account_id','user_id']);
        
        $size_only = collect($request->only('type_size_id','title','statut','photo','photo_dir'))
        ->put('account_id',$account_user->account_id)
        ->put('user_id',$account_user->user_id)
        ->all();

        $size = size::create($size_only);
    
        return response()->json([
            'statut' => 1,
            'size ' => $size,
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

        $size = size::where('id',$id)->where('account_id', $account_user->account_id)->first();

        return response()->json([
            'statut' => 1,
            'size' => $size
        ]);
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'statut' => 'required',
            'photo' => 'required',
            'photo_dir' => 'required',
            'type_size_id'=>'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Errors' => $validator->errors()
            ]);
        }
        $account_user = account_user::where('user_id',Auth::user()->id)
            ->first(['account_id','user_id']);

        $size_only = collect($request->all())
            ->only('type_size_id', 'title', 'statut', 'photo', 'photo_dir')
            ->put('account_id', $account_user->account_id)
            ->all();

        $size = size::find($id)
            ->update($size_only);
        $size_updated = size::find($id);


        return response()->json([
            'statut' => 1,
            'size' => $size_updated,
        ]);
    }


    public function destroy($id)
    {
        $size = size::where('id',$id)->delete();
        return response()->json([
            'statut' => 'deleted successfuly',
            'size deleted ' => $size,
        ]);
    }
}
