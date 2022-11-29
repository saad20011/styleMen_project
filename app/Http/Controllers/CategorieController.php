<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\categorie;
use App\Models\User;
use App\Models\account_user;
use App\Models\account;
use Auth;
use Validator;
class CategorieController extends Controller
{
    public $account_user ;



    public function index(Request $request)
    {
        $account_user = account_user::where('user_id',Auth::user()->id)
            ->first(['account_id','user_id']);
        $categories = categorie::where('account_id',$account_user->account_id)
            ->get();

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
            'photo' => '',
            'photo_dir' => '',
        ]);

        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };
        
        $account_user = account_user::where('user_id',Auth::user()->id)
            ->first(['account_id','user_id']);
        
        $categorie_only = collect($request->only('title','statut', 'photo', 'photo_dir'))
            ->put('account_id',$account_user->account_id)
            ->put('user_id',$account_user->user_id)
            ->all();
        $categorie = categorie::create($categorie_only);
    
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
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'statut' => 'required',
            'photo' => '',
            'photo_dir' => '',
        ]);

        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };

        $account_user = account_user::where('user_id',Auth::user()->id)
        ->first(['account_id','user_id']);
    
        $categorie_only = collect($request->all())
            ->only('title','statut','photo','photo_dir')
            ->put('account_id', $account_user->account_id)
            ->put('user_id', $account_user->user_id)
            ->all();
        $categorie = categorie::find($id)
            ->update($categorie_only);
        $categorie_updated = categorie::find($id);

        return response()->json([
            'statut' => 1,
            'categorie' => $categorie_updated,
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
