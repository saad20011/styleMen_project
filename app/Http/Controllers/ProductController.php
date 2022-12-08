<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ImageController;
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
        $product_variables = account::with('offers', 'type_sizes', 'depots', 'suppliers')
            ->where('id',$account->id)
            ->first();
        return response()->json([
            'statut' => 1,
            'data' => $product_variables,
        ]);

    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'reference' => 'required',//|unique:products
            'title' => 'required',//|unique:products
            'statut' => 'required',
            'sizes' => 'required|array|min:1',
            'sizes.*'=>'required|exists:sizes,id',
            'offers' => 'required|array|min:1',
            'offers.*'=>'required|exists:offers,id',
            'principal_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'images' => 'array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'suppliers' => 'array',
            'category_id'=>'required|exists:categories,id',
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Errors' => $validator->errors()
            ]);
        }
        $account_user = User::find(Auth::user()->id)->account_user->first();
        $store_product = account_user::find($account_user->id)->products()->create($request->all());
        $account = User::find(Auth::user()->id)->accounts->first();
        $account->products()->attach($store_product->id, ['category_id' => $request->category_id]);

        $new_product = product::find($store_product->id);

        // attacher les sizes a le produit à la table product_size 
        $new_product->sizes()->attach($request->sizes);
        $product_sizes = product_size::where('product_id',$new_product->id)->get();
        $depots = account::find($account_user->account_id)->depots('id')->pluck('id')->all();

        // attacher les depots a les product_size à la table product_depot
        foreach($product_sizes as $product_size){
            product_size::find($product_size->id)->depots()->attach($depots);
        }
        
        // attacher les offres a le produit à la table product_offer 
        $new_product->account_product->first()->offers()->attach($request->offers);

        // ajouter une image principal du produit
        $new_product->image = ImageController::store(new Request(['image'=>$request->principal_image]), $local=1,$new_product,'products', $principal_image=1 );

        // ajouter des images secondaire du produit
        if(count($request->images)>0){
            $images = [];
            foreach($request->images as $image){
                $image_arr = ImageController::store(new Request(['image'=>$image]), $local=1,$new_product,'products');
                array_push($images,$image_arr );
            }
            $new_product->images = $images;
        }

        return response()->json([
            'statut' => 12,
            'data' => $new_product,

        ]);
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $account_user = User::find(Auth::user()->id)->account_user->first();
        $product = product::where(['id'=>$id, 'account_user_id'=> $account_user->id])->first();
        if(!$product)
            return response()->json([
                'statut' => 0,
                'data' => 'not found',
            ]);

        $products = product::with('product_size', 'sizes', 'suppliers','account_product', 'images')
            ->where(['account_user_id'=> $account_user->id,'id'=> $id])
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
        
        $product_variables = account::with('offers', 'type_sizes', 'depots', 'suppliers')
            ->where('id',$account_user->account_id)
            ->first();


        return response()->json([
            'statut' => 1,
            'product ' => $products,
            'product_variables ' => $product_variables,
        ]);

    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->input(),[
            'reference' => 'required',//|unique:products
            'title' => 'required',//|unique:products
            'statut' => 'required',
            'principal_image' => 'required|array|between:1,1',
            'principal_image' => 'required|exists:images,id',
            'images.*' => 'required|exists:images,id',
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
        $product->principal_image =  ImageController::update(new Request(['image'=>$request->principal_image[0]]),'', $local=1,$product,$request->principal_image, 'Product', $principal_image=1);

        if(count($request->images)>0){
            foreach($request->images as $image){
                ImageController::update(new Request(['image'=>$image]),'', $local=1,$product,$request->images, 'Product');
            }
        }
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


    public function store_product_suppliers($supplier_id, $suppliers){
        $product_size = product::find($supplier_id)
                ->suppliers()->attach($suppliers);
    }
}