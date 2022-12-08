<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\size;
use App\Models\account;
use App\Models\User;
use App\Models\type_size;
use App\Models\account_user;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SizeController extends Controller
{

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type_size_id'=>'required|exists:type_sizes,id',
            'pagination' => 'required',
            'order_by' => '',
            'serach_by' => '',
            'filter_by_columns' => ''
        ]);
        
        if($validator->fails()){
            return response()->json([
                'Validation Errors' => $validator->errors()
            ]);
        }
        $account = User::find(Auth::user()->id)->accounts->first();
        $sizes = account::find($account->id)->sizes()
        ->where('type_size_id', $request->type_size_id)
        ->paginate(20);


        return response()->json([
            'statut ' => 1,
            'data' => $sizes,
        ]);
    }

    public function create(Request $request)
    {
        $account = User::find(Auth::user()->id)->accounts->first();
        $sizes = account::find($account->id)->type_sizes()->paginate(20);

        return response()->json([
            'statut ' => 1,
            'data' => $sizes
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|unique:size',
            'statut' => 'required',
            'type_size_id'=>'required|exists:type_sizes,id'
        ]);

        if($validator->fails()){
            return response()->json([
                'Validation Errors' => $validator->errors()
            ]);
        }
        $account = User::find(Auth::user()->id)->accounts->first();
        $size = size::create($request->all());
    
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
        $account = User::find(Auth::user()->id)->accounts->first();
        $size = size::find($id);

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

        $size = size::find($id)->update($size_only);
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
