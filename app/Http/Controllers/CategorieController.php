<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\categorie;
use App\Models\User;
use App\Models\account;

class CategorieController extends Controller
{

    public function index(Request $request)
    {
        $categories = categorie::get();

        return response()->json([
            'categories '=>$categories,
        ]);
    }

    public function create(Request $request)
    {
        $users = User::get();
        $accounts = account::get();

        return response()->json([
            'statut' => 0,
            'users ' => $users,
            'accounts ' => $accounts,
        ]);

    }

    public function store(Request $request)
    {
        request()->validate([
            'title' => 'required|unique:categories',
            'statut' => 'required',
            'photo' => '',
            'photo_dir' => '',
            'account_id' => 'required',
            'user_id' => 'required'
        ]);
    
        $categorie = categorie::create($request->all());
    
        return response()->json([
            'statut' => 'categorie created successfuly',
            'categorie' => $categorie,
        ]);
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $categorie = categorie::find($id);

        return response()->json([
            'statut' => 0,
            'categorie ' => $categorie
        ]);

    }

    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'title' => 'required|unique:categories',
            'statut' => 'required',
            'photo' => '',
            'photo_dir' => '',
            'account_id' => 'required',
            'user_id' => 'required'
        ]);
        $categorie = categorie::find($id);
        $categorie->title = $request->input('title');
        $categorie->statut = $request->input('statut');
        $categorie->save();
        return response()->json([
            'statut' => 'your phone type is updated successfuly',
            'categorie' => $categorie,
        ]);
    }


    public function destroy($id)
    {
        $categorie_b =  categorie::find($id);
        $categorie = categorie::find($id)->delete();
        return response()->json([
            'statut' => 'deleted successfuly',
            'categorie' => $categorie_b,
        ]);
    }
}
