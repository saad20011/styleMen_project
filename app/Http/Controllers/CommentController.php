<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\comment;
use App\Models\account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $comments = comment::get();

        return response()->json([
            'statut'=>1,
            'data'=>$comments,
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
            'current_statut' => 'required',
            'post_poned' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };
        $account = User::find(Auth::user()->id)->accounts->first();
        $request->merge(["account_id"=>$account->id]);
        $created = comment::create($request->all());
        if($created){
            return response()->json([
                'statut' => 1,
                'data' => $created,
            ]);
        }
        return response()->json([
            'statut' => 0,
            'data' => "add comment error",
        ]);
        
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $comment = comment::find($id);

        return response()->json([
            'statut' => 1,
            'data' => $comment,
        ]);
    }


    public function update(Request $request, $id)
    {
        $account = User::find(Auth::user()->id)->accounts->first();
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'statut' => 'required',
            'current_statut' => 'required',
            'post_poned' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };
        $comment = comment::find($id)->update($request->all());
        $comment=comment::find($id);
        return response()->json([
            'statut' => 1,
            'data' => $comment,
        ]);
        
    }


    public function destroy($id)
    {
        $comment = comment::where('id',$id)->delete();
        return response()->json([
            'statut' => 'deleted successfuly',
            'data' => $comment,
        ]);
    }
}
