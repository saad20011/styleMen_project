<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\categorie;
use App\Models\User;
use App\Models\account_user;
use Auth;
use Validator;
class CategorieController extends Controller
{
    public $account_user ;



    public function index(Request $request)
    {
        $account_user = User::find(Auth::user()->id)->account_user->first();
        $categories = account_user::find($account_user->id)->categories;

        return response()->json([
            'categories '=>$categories,
        ]);
    }

    public function create(Request $request)
    {
        //
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'statut' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };
        
        $account_user = User::find(Auth::user()->id)->account_user->first();

        
        $categorie_only = collect($request->only('title','statut'))
            ->put('account_user_id', $account_user->id)
            ->all();
        $categorie = account_user::find($account_user->id)
            ->categories()
            ->create($categorie_only);

        return response()->json([
            'statut' => 1,
            'data' => $categorie,
        ]);
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $account_user = User::find(Auth::user()->id)->account_user->first();
        $categories = account_user::find($account_user->id)->categories;
        return response()->json([
            'statut' => 0,
            'data ' => $categories
        ]);

    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'statut' => 'required',

        ]);

        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };
        $account_user = User::find(Auth::user()->id)->account_user->first();

        $categorie_only = collect($request->all())
            ->only('title','statut')
            ->put('account_user_id', $account_user->id)
            ->all();
        $categorie = categorie::find($id)
            ->update($categorie_only);
        $categorie_updated = categorie::find($id);

        return response()->json([
            'statut' => 1,
            'data' => $categorie_updated,
        ]);
    }


    public function destroy($id)
    {
        $categorie_b =  categorie::find($id);
        $categorie = categorie::find($id)->delete();
        return response()->json([
            'statut' => 'deleted successfuly',
            'data' => $categorie_b,
        ]);
    }
}
