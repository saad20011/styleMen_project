<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\subcomment;
use App\Models\account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SubcommentController extends Controller
{
    public function index(Request $request)
    {
        $subcomments = subcomment::get();

        return response()->json([
            'statut'=>1,
            'data'=>$subcomments,
        ]);
    }

    public function create(Request $request)
    {

    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'account_user_id' => 'required',
            'comment_id' => 'required',
            'order_change' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };
        $account_user = User::find(Auth::user()->id)->account_user->first();
        $request->merge(["account_user_id"=>$account_user->id]);
        $subcomment = subcomment::create($request->all());
        return response()->json([
            'statut' => 1,
            'data' => $subcomment,
        ]);
        
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $subcomment = subcomment::find($id);

        return response()->json([
            'statut' => 1,
            'data' => $subcomment,
        ]);
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'comment_id' => 'required',
            'order_change' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };
        $subcomment = subcomment::find($id)->update($request->all());
        $subcomment=subcomment::find($id);
        return response()->json([
            'statut' => 1,
            'data' => $subcomment,
        ]);
    }


    public function destroy($id)
    {
        $subcomment = subcomment::where('id',$id)->delete();
        return response()->json([
            'statut' => 'deleted successfuly',
            'data' => $subcomment,
        ]);
    }
}
