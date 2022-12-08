<?php

namespace App\Http\Controllers;

use App\Models\account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
class AccountController extends Controller
{

    public function index(Request $request)
    {

        $accounts = account::with('users')->get();

        return response()->json([
            'statut' => 1,
            'accounts' => $accounts,
        ]);
    }

    public function create(Request $request)
    {

    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:accounts',
            'prefixe' => 'required',
            'counter' => 'required',
            'statut' => 'required',
            'photo' => 'required',
            'photo_dir' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };
        $account = account::create($request->all());
    
        return response()->json([
            'statut' => 1,
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

        ]);
        }


    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'prefixe' => 'required',
            'counter' => 'required',
            'statut' => 'required',
            'photo' => 'required',
            'photo_dir' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };

        $account_only = collect($request->only('name','prefixe','counter','statut','photo','photo_dir'));
        $account = account::find($id)->update($account_only->all());
        $account_updated = account::find($id)->get();
       return response()->json([
            'statut' => 1,
            'account' => $account_updated,
        ]);
    }


    public function destroy($id)
    {
        $account_b =  account::find($id);
        $account = account::find($id)->delete();
        return response()->json([
            'statut' => 1,
            'account' => $account_b,
        ]);
    }
}
