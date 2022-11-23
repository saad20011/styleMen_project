<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\source;
use App\Models\account_user;
use Auth;
use Validator;
use DB;
class sourceController extends Controller
{
    public function index(Request $request)
    {
        $sources = source::get();

        return response()->json([
            'statut' => 1,
            'sources ' => $sources,
        ]);
    }

    public function create(Request $request)
    {

    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'photo' => 'required',
            'photo_dir' => 'required',
            'statut' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };

        $account_user = account_user::where('user_id',Auth::user()->id)
            ->first(['account_id','user_id']);
            
        $source_only = collect($request->only('title','website','email','photo','photo_dir','statut'))
            ->put('account_id',$account_user->account_id)->all();
        $source = source::create($source_only);
    
        return response()->json([
            'statut' => 'product created successfuly',
            'source' => $source,
        ]);
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $source = source::find($id);
        return response()->json([
            'statut' => 1,
            'source' => $source,
        ]);
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'photo' => 'required',
            'photo_dir' => 'required',
            'statut' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };
        $account_user = DB::table('account_users')
        ->join('users','users.id', '=', 'account_users.user_id')
        ->join('accounts','accounts.id', '=', 'account_users.account_id')
        ->where('users.id',Auth::user()->id)
        ->select('accounts.name as account_name',
                'accounts.id as account_id',
                'users.id as user_id'
        )->first();
        $source_only = collect($request->only('title','website','email','photo','photo_dir','statut'));

        $source = source::find($id)->update($source_only->all());
        $source_updated = source::where('id',$id)->get();

        return response()->json([
            'statut' => 1,
            'source' => $source_updated,
        ]);
    }


    public function destroy($id)
    {
        $source_deleted = source::where('id',$id)->get();
        $source = source::where('id',$id)->delete();
        return response()->json([
            'statut' => 1,
            'source' => $source_deleted,
        ]);
    }
}
