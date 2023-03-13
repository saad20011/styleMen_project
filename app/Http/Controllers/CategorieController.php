<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\categorie;
use App\Models\User;
use App\Models\account_user;
use App\Models\account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
class CategorieController extends Controller
{
    public $account_user ;

    public static function index(Request $request, $local=0, $columns=['id', 'title', 'products'], $paginate=1)
    {
        $account_user = User::find(Auth::user()->id)->account_user->first();
        $accounts_users = account::find($account_user->account_id)->account_user->pluck('id')->toArray();
        $filters = OfferController::filterColumns($request->toArray(), ['reference', 'title', 'products', 'suppliers', 'variations', 'offers']);
        $categories = categorie::whereIn('account_user_id' , $accounts_users)
        ->where(function($query) use ($filters) {
                if($filters['search'] == null){
                    //filtering by columns
                    if($filters['filters']['title'] != null){
                        $query->where('title', 'like', "%{$filters['filters']['title']}%" );
                    }
                }else{
                    $query->where('title', 'like', "%{$filters['search']}%" );
                }
            })
            ->with(['products'])->get()
            ->map(function($category) use($columns, $filters){
                $category->products = $products = $category->products->map(function($product){
                    return $product->only('id', 'title');
                });
                $productsExisting = HelperFunctions::filterExisting($filters['filters']['products'], $products->pluck('id'));
                if($productsExisting){
                    return $category->only($columns);
                }
    })->filter()->values();
    $dataPagination = HelperFunctions::getPagination($categories, intval($filters['pagination']['per_page']), intval($filters['pagination']['current_page']));
    if($local == 1){
        if($paginate == false){
            return $categories->toArray();
        }
        return $dataPagination;
    };
    return response()->json([
            'statut' => 1,
            'data' => $dataPagination,
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
