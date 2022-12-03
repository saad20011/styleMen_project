<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\product;
use App\Models\account_user;
use App\Models\account_product;
use App\Models\account;
use App\Models\product_size;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\imageable;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        $account_user = User::find(Auth::user()->id)->account_user->first();
        $products = product::with('product_size', 'sizes', 'suppliers','account_product', 'images')
            ->where('account_user_id', $account_user->id)
            ->get();
        foreach($products as $product){
            $product->depots = collect($product->product_size)->map(function($product_size){
                $depot = product_size::find($product_size->id)->depots;
                return $depot;
            })->collapse()->unique('code');

            $product->offers = collect($product->account_product)->map(function($account_product){
                $offers = account_product::find($account_product->id)->offers;
                return $offers;
            })->collapse()->unique('title');
            collect($product)->except('product_size', 'account_product');
        }

        
        return response()->json([
            'statut' => 1,
            'data' => $products
        ]);
    }

    public function create(Request $request)
    {
        $account = User::find(Auth::user()->id)->accounts->first();
        $create_product = account::with('offers', 'type_sizes', 'depots', 'suppliers');

        return response()->json([
            'statut' => 1,
            'data' => $create_product,
        ]);

    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'reference' => 'required',//|unique:products
            'title' => 'required',//|unique:products
            'statut' => 'required',
            'principal_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:20480',

        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Errors' => $validator->errors()
            ]);
        }
        $account_user = User::find(Auth::user()->id)->account_user->first();
        $new_product = account_user::find($account_user->id)->products()->create($request->all());
        $new_product->principal_image = $this->store_product_imageables($new_product->id, $account_user->account_id,$request->principal_image,2 );
        if(count($new_product->images)>0){
            foreach($new_product->images as $image){
                $this->store_product_imageables($new_product->id, $account_user->account_id,$image,1 );
            }
        }
        return response()->json([
            'statut' => 1,
            // 'data' => $new_product,

        ]);
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $account_user = User::find(Auth::user()->id)->account_user->first();
        $product = product::with('offers', 'type_sizes', 'depots', 'suppliers')
            ->where(['id' => $id, 'account_user_id' => $account_user->id])->first();
        $product_variables = account::with('offers', 'type_sizes', 'depots', 'suppliers');


        return response()->json([
            'statut' => 1,
            'product ' => $product,
            'product_variables ' => $product_variables,
        ]);

    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->input(),[
            'reference' => 'required',//|unique:products
            'title' => 'required',//|unique:products
            'statut' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Errors' => $validator->errors()
            ]);
        }

        $account_user = User::find(Auth::user()->id)->account_user->first();
        $product = product::where(['id'=>$id, 'account_user_id'=> $account_user->id])->first();
        if(!$product)
            return response()->json([
                'statut' => 0,
                'data' => 'not found',
            ]);

        $product->update($request->all());
        $product_updated = product::find($id);

        return response()->json([
            'statut'=>1,
            'product' => $product_updated
        ]);
    }
    

    public function destroy($id)
    {
        $product_b =  product::find($id);
        $product = product::find($id)->delete();
        return response()->json([
            'statut' => 'deleted successfuly',
            'product' => $product_b,
        ]);
    }

    public function store_product_imageables($product_id, $account_id, $image, $principal=1)
    {
        // add images to a product
        $path = Storage::disk('public')->putFile('images/products', $image); //store the image
        $product = product::find($product_id);
        $image = account::find($account_id)->has_images()->create([
            'account_id' => $account_id,
            'title'=> 'brand',
            'photo'=> $path,
            'photo_dir'=>'/storage',
        ]);
        $product->images()->attach($image,['statut' => $principal]);
        dd('$path');

        // return $product;
    }

    public function store_product_sizes(){
        // add sizes to a product
    }
    public function store_product_depots(){
        // add depots to a product
    }
    public function store_product_offers(){
        // add offers to a product
    }
    public function store_product_suppliers(){
        // add suppliers to a product
    }
}