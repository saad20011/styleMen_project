<?php

namespace App\Http\Controllers;

use App\Models\account;
use Illuminate\Http\Request;

class AccountController extends Controller
{

    public function index(Request $request)
    {
        $accounts = account::get();

        return response()->json([
            'statut' => 1,
            'account' => $accounts,

        ]);
    }

    public function create(Request $request)
    {

    }

    public function store(Request $request)
    {
        request()->validate([
            'name' => 'required|unique:accounts',
            'prefixe' => 'required',
            'counter' => 'required',
            'statut' => 'required',
            'photo' => 'required',
            'photo_dir' => 'required',
        ]);
    
        $account = account::create($request->all());
    
        return response()->json([
            'statut' => 'product created successfuly',
            'product' => $account,
        ]);
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $accounts = account::find($id);

        return response()->json([
            'statut' => 1,
            'account' => $accounts,

        ]);    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'prefixe' => 'required',
            'counter' => 'required',
            'statut' => 'required',
            'photo' => 'required',
            'photo_dir' => 'required',
        ]);
        $account = account::find($id);
        $account->name = $request->input('name');
        $account->prefixe = $request->input('prefixe');
        $account->counter = $request->input('counter');
        $account->statut = $request->input('statut');
        $account->photo = $request->input('photo');
        $account->photo_dir = $request->input('photo_dir');

        $account->save();
        return response()->json([
            'statut' => 'your account is updated successfuly',
            'account' => $account,
        ]);
    }


    public function destroy($id)
    {
        $account_b =  account::find($id);
        $account = account::find($id)->delete();
        return response()->json([
            'statut' => 'deleted successfuly',
            'account' => $account_b,
        ]);
    }
}
