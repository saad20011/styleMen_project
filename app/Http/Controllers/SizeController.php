<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\size;
use App\Models\account;
use App\Models\User;

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

        return response()->json([
            'statut ' => 1,
            'accounts ' => $accounts,
            'users ' => $users
        ]);
    }

    public function store(Request $request)
    {
        request()->validate([
            'title' => 'required|unique:size',
            'statut' => 'required',
            'account_id' => 'required',
            'user_id' => 'required',
            'photo' => 'required',
            'photo_dir' => 'required',
        ]);
    
        $size = size::create($request->all());
    
        return response()->json([
            'statut' => 'size created successfuly',
            'size ' => $size,
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
        $this->validate($request, [
            'title' => 'required',
            'statut' => 'required',
            'account_id' => 'required',
            'user_id' => 'required',
            'photo' => 'required',
            'photo_dir' => 'required',
        ]);
        $size = size::find($id);
        $size->title = $request->input('title');
        $size->statut = $request->input('statut');
        $size->account_id = $request->input('account_id');
        $size->user_id = $request->input('user_id');
        $size->photo = $request->input('photo');
        $size->photo_dir = $request->input('photo_dir');

        $size->save();
        return response()->json([
            'statut' => 'your size is updated successfuly',
            'size' => $size,
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
