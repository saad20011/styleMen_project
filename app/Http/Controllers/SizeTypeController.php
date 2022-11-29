<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\type_size;
use App\Models\User;
use App\Models\account_user;
use Validator;
use Auth;

class SizeTypeController extends Controller
{

    
    public function index(Request $request)
    {
        
        $account_user = account_user::where('user_id',Auth::user()->id)
            ->first(['account_id','user_id']);
        $type_sizes = type_size::where('account_id',$account_user->account_id)
            ->get();

        return response()->json([
            'statut' => 1,
            'type_size' => $type_sizes,
        ]);
    }

    public function create(Request $request)
    {

    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };

        $account_user = account_user::where('user_id',Auth::user()->id)
            ->first(['account_id','user_id']);

        $type_size_only = collect($request->only('name','description'))
            ->put('account_id',$account_user->account_id)
            ->put('user_id',$account_user->user_id)
            ->all();

        $type_size = type_size::create($type_size_only);
    
        return response()->json([
            'statut' => 1,
            'type_size' => $type_size,
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
            'name' => 'required',
            'description' => 'required',

        ]);

        $account_user = account_user::where('user_id',Auth::user()->id)
        ->first(['account_id','user_id']);
    
        $type_size_only = collect($request->all())
            ->only('name','description')
            ->put('account_id', $account_user->account_id)
            ->put('user_id', $account_user->user_id)
            ->all();
            
        $type_size = type_size::find($id)
            ->update($type_size_only);
        $type_size_updated = type_size::find($id);

        return response()->json([
            'statut' => 1,
            'type_size' => $type_size_updated,
        ]);
    }


    public function destroy($id)
    {
        $type_size_b =  type_size::find($id);
        $type_size = type_size::find($id)->delete();
        return response()->json([
            'statut' => 'deleted successfuly',
            'type_size' => $type_size_b,
        ]);
    }
}
