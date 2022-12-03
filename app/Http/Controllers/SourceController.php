<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\source;
use App\Models\User;
use App\Models\account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class sourceController extends Controller
{
    public function index(Request $request)
    {
        $account = User::find(Auth::user()->id)->accounts->first();
        $sources = account::find($account->id)->sources()->paginate(20);

        return response()->json([
            'statut' => 1,
            'data' => $sources,
        ]);
    }

    public function create(Request $request)
    {

    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'statut' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };
        $account = User::find(Auth::user()->id)->accounts->first();
        $source = account::find($account->id)->sources()->create($request->all());

        return response()->json([
            'statut' => 1,
            'data' => $source,
        ]);
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $account = User::find(Auth::user()->id)->accounts->first();
        $source = source::where(['id'=>$id, 'account_id'=> $account->id])
                    ->first();
        if(!$source)
            return response()->json([
                'statut' => 0,
                'data' => 'not found',
            ]);
        return response()->json([
            'statut' => 1,
            'data' => $source,
        ]);
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'statut' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };
        $account = User::find(Auth::user()->id)->accounts->first();
        $source = source::where(['id'=>$id, 'account_id'=> $account->id])->first();
        if(!$source)
            return response()->json([
                'statut' => 0,
                'data' => 'not found',
            ]);

        $source->update($request->all());
        $source_updated = source::find($id);

        return response()->json([
            'statut' => 1,
            'data' => $source_updated,
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
