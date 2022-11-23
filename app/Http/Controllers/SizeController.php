<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\size;
use App\Models\account;
use App\Models\User;
use App\Models\type_size;
use Validator;
class SizeController extends Controller
{

    public function index(Request $request)
    {
        $sizes = size::get();

        return response()->json([
            'statut ' => 1,
            'sizes ' => $sizes,
        ]);
    }

    public function create(Request $request)
    {
        $accounts = account::get();
        $users = User::get();
        $type_sizes = type_size::get();

        return response()->json([
            'statut ' => 1,
            'accounts ' => $accounts,
            'users ' => $users,
            'type_sizes ' => $type_sizes
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|unique:size',
            'statut' => 'required',
            'account_id' => 'required',
            'user_id' => 'required',
            'photo' => 'required',
            'photo_dir' => 'required',
            'type_size_id'=>'required'
        ]);
        $size_col = collect($request->all())->only('type_size_id','title','statut','account_id','photo','photo_dir','user_id')->toArray();
        $size = size::create($size_col);
    
        return response()->json([
            'statut' => 'size created successfuly',
            'size ' => $request->all(),
        ]);
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $size = size::where('id',$id)->first();

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
            'account_id' => 'required',
            'user_id' => 'required',
            'photo' => 'required',
            'photo_dir' => 'required',
            'type_size_id'=>'required'

        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Errors' => $validator->errors()
            ]);
        }
        $size_col = collect($request->all())
            ->only('type_size_id','title','statut','account_id','user_id','photo','photo_dir')
            ->toArray();
        $size = size::find($id)
            ->update($size_col);
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
