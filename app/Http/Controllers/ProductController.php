<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\product;
use App\Models\account_user;
use App\Models\type_size;
use App\Models\categorie;
use App\Models\offer;
use App\Models\products_size;
use App\Models\products_offer;
use App\Models\products_supplier;
use App\Models\accounts_products;
use App\Models\depot;
use Illuminate\Support\Facades\DB;
use Auth;  
use Validator;
class ProductController extends Controller
{

    public function index(Request $request)
    {
        $cities_carrier = DB::table('accounts')
        ->join('accounts_products', 'accounts_products.account_id', '=', 'accounts.id')
        ->join('products', 'products.id', '=', 'accounts_products.product_id')
        ->join('categories', 'categories.id', '=', 'accounts_products.category_id')
        ->leftJoin('products_offers', 'products_offers.product_id', '=', 'products.id')
        ->leftJoin('offers', 'offers.id', '=', 'products_offers.offer_id')
        ->leftJoin('products_sizes', 'products_sizes.product_id', '=', 'products.id')
        ->leftJoin('sizes', 'sizes.id', '=', 'products_sizes.size_id')
        ->leftJoin('products_suppliers', 'products_suppliers.product_id', '=', 'products.id')
        ->leftJoin('suppliers', 'suppliers.id', '=', 'products_suppliers.supplier_id')
        // ->where('accounts.id','2')
        ->where('products_sizes.statut','1')
        ->where('products_offers.statut','1')
        ->where('products_suppliers.statut','1')
        ->where('accounts_products.statut','1')
        ->orderBy('products.title')
        ->select('accounts.id as account_id',
                'accounts_products.id as accounts_products_id',
                'products.title as product_name',
                'categories.title as categories_title',
                'offers.title as offers_title',
                'offers.price as offers price',
                'products_sizes.product_id as product_size_id',
                'sizes.title as sizes',
                'products_suppliers.product_id as product_supplier_id',
                'suppliers.title as suppliers_title',
        )
        ->get()->toArray();
        $distinc = array();
        foreach($cities_carrier as $key=>$value){
            if(array_key_exists($cities_carrier[$key]->product_name, $distinc)==false){
                $element = array(
                    "product_name"=>$cities_carrier[$key]->product_name,
                    "sizes"=>array($cities_carrier[$key]->sizes),
                    "categories"=>array($cities_carrier[$key]->categories_title),
                    "offers"=>array($cities_carrier[$key]->offers_title),
                    "suppliers"=>array($cities_carrier[$key]->suppliers_title),
                    );
                $distinc[$cities_carrier[$key]->product_name]= $element;
            }else{

                if(in_array($cities_carrier[$key]->sizes, $distinc[$cities_carrier[$key]->product_name]["sizes"])==false ){
                    array_push($distinc[$cities_carrier[$key]->product_name]["sizes"],$cities_carrier[$key]->sizes);
                }
                if(in_array($cities_carrier[$key]->categories_title, $distinc[$cities_carrier[$key]->product_name]["categories"])==false ){
                    array_push($distinc[$cities_carrier[$key]->product_name]["categories"],$cities_carrier[$key]->categories_title);
                }
                if(in_array($cities_carrier[$key]->offers_title, $distinc[$cities_carrier[$key]->product_name]["offers"])==false ){
                    array_push($distinc[$cities_carrier[$key]->product_name]["offers"],$cities_carrier[$key]->offers_title);
                }
                if(in_array($cities_carrier[$key]->suppliers_title, $distinc[$cities_carrier[$key]->product_name]["suppliers"])==false ){
                    array_push($distinc[$cities_carrier[$key]->product_name]["suppliers"],$cities_carrier[$key]->suppliers_title);
                }
            }
          }

        return response()->json([
            'products '=> array_values($distinc),
        ]);
    }

    public function create(Request $request)
    {
        $account_users = account_user::get();
        $type_size = type_size::get(['id','name']);
        $categorie = categorie::get(['id','title','statut']);
        $offer = DB::table('offers')
        ->join('brands', 'offers.brand_id', '=', 'brands.id')
        ->select('offers.id',
                'offers.title',
                'offers.price',
                'offers.shipping_price',
                'offers.statut',
                'brands.title as brand_name',
        )->get();

        return response()->json([
            'statut' => 1,
            'offer ' => $offer,
            'type_size ' => $type_size,
            'categorie ' => $categorie,
        ]);

    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            '.*reference' => 'required',//|unique:products
            '.*title' => 'required',//|unique:products
            '.*link' => 'required',//|unique:products
            '.*price' => 'required',
            '.*sellingprice' => 'required',
            '.*account_user_id' => 'required',
            '.*photo' => 'required',
            '.*photo_dir' => 'required',
            '.*statut' => 'required',
            '.*offer_id' => 'required',
            '.*categorie_id' => 'required',
            '.*size_id' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Errors' => $validator->errors()
            ]);
        }
        $account_user = DB::table('account_users')
        ->join('users','users.id', '=', 'account_users.user_id')
        ->join('accounts','accounts.id', '=', 'account_users.account_id')
        ->where('users.id',Auth::user()->id)
        ->select('accounts.name as account_name',
                'accounts.id as account_id',
                'users.id as user_id'
        )->first();

        $products = collect($request->all())->map(function ($product_item) use($account_user) {
            $coll = collect($product_item)->put('account_id',$account_user->account_id)->toArray();
            $product = product::create($coll);
            
            $product_offers = collect($product_item['offers'])->map(function($offer_id) use($product){
                $product_offer = products_offer::create([
                    'offer_id'=> $offer_id,
                    'product_id'=>$product['id'] ,
                ]);          
                return $product_offer ;      
            });
            $product_sizes = collect($product_item['sizes'])->map(function($size_id) use($product,$account_user){
                $product_size = products_size::create([
                    'size_id'=>$size_id,
                    'product_id'=>$product['id'] ,
                    'account_id'=>$account_user->account_id ,
                    'user_id'=> $account_user->user_id,
                ]);    
                return $product_size ;      
            
            });
            $product_categories = collect($product_item['categories'])->map(function($categorie_id) use($product,$account_user){
                $product_categorie = accounts_products::create([
                    'account_id'=>$account_user->account_id ,
                    'category_id'=>$categorie_id,
                    'product_id'=>$product['id'] ,
                ]);                
                return $product_categorie ;      

            });
            $product['offers']=$product_offers;
            $product['sizes']=$product_sizes;
            $product['categories']=$product_categories;

            return $product;
        });

        return response()->json([
            'statut' => 'product created successfuly',
            'product' => $products,

        ]);
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $product = product::find($id);
        $type_size = type_size::get(['id','name']);
        $categorie = categorie::get(['id','title','statut']);
        $offer = DB::table('offers')
        ->join('brands', 'offers.brand_id', '=', 'brands.id')
        ->select('offers.id',
                'offers.title',
                'offers.price',
                'offers.shipping_price',
                'offers.statut',
                'brands.title as brand_name',
        )->get();

        return response()->json([
            'statut' => 1,
            'product ' => $product,
            'offer ' => $offer,
            'type_size ' => $type_size,
            'categorie ' => $categorie,
        ]);

    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->input(),[
            'reference' => 'required',//|unique:products
            'title' => 'required',//|unique:products
            'link' => 'required',//|unique:products
            'price' => 'required',
            'sellingprice' => 'required',
            'account_user_id' => 'required',
            'photo' => 'required',
            'photo_dir' => 'required',
            'statut' => 'required',
            'offers' => 'required',
            'categories' => 'required',
            'sizes' => 'required',
            'suppliers' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Errors' => $validator->errors()
            ]);
        }
        $account_user = DB::table('account_users')
        ->join('users','users.id', '=', 'account_users.user_id')
        ->join('accounts','accounts.id', '=', 'account_users.account_id')
        ->where('users.id',Auth::user()->id)
        ->select('accounts.name as account_name',
                'accounts.id as account_id',
                'users.id as user_id'
        )->first();
        $product_col = collect($request->all())->only('reference','title','link','price','sellingprice','account_user_id','photo','photo_dir','statut')->toArray();
        $product = product::find($id)->update($product_col);
        $product_updated = product::find($id);
        
        // change sizes product
        $product_sizes = DB::table('products_sizes')
            ->join('products', 'products.id', '=', 'products_sizes.product_id')
            ->join('sizes', function ($join) use($id) {
                $join->on('sizes.id', '=', 'products_sizes.size_id')
                    ->where('products_sizes.product_id', '=', $id);
            })
            ->select('sizes.id as size_id',
                    'products.id as product_id',
                    'products_sizes.id as product_size_id',
                    'products_sizes.statut as product_size_statut',
            )->get();
        $request_sizes = $request->input('sizes');
        $reactive_or_add_size = collect($request_sizes)->map(function($request_size) use($product_sizes,$account_user,$product_updated){
            $in_array = collect($product_sizes->whereIn('size_id', [$request_size]))->first();
            if($in_array == null){
                //not exist
                $product_size = products_size::create([
                    'size_id'=>$request_size,
                    'product_id'=>$product_updated['id'] ,
                    'account_id'=>$account_user->account_id ,
                    'user_id'=> $account_user->user_id,
                ]);    
                return $product_size;
            }else{
                // exist check
                if($in_array->product_size_statut==0){
                    $product = products_size::where('id',$in_array->product_size_id)->update(['statut'=>1]);
                
                return $in_array;
            }}
            
        });
        $desactive_size = collect($product_sizes)->map(function($product_size) use($request_sizes){
            $in_array = in_array($product_size->size_id, $request_sizes);
            if($in_array == false){
                // exist
                $product = products_size::where('id',$product_size->product_size_id)->update(['statut'=>0]);
        }});

        // change offers product
        $product_offers = DB::table('products_offers')
            ->join('products', 'products.id', '=', 'products_offers.product_id')
            ->join('offers', function ($join) use($id) {
                $join->on('offers.id', '=', 'products_offers.offer_id')
                    ->where('products_offers.product_id', '=', $id);
            })
            ->select('offers.id as offer_id',
                    'products.id as product_id',
                    'products_offers.id as product_offer_id',
                    'products_offers.statut as product_offer_statut',
            )->get();
        $request_offers = $request->input('offers');
        $reactive_or_add_offer = collect($request_offers)->map(function($request_offer) use($product_offers,$account_user,$product_updated){
            $in_array = collect($product_offers->whereIn('offer_id', [$request_offer]))->first();
            if($in_array == null){
                //not exist
                $product_offer = products_offer::create([
                    'offer_id'=>$request_offer,
                    'product_id'=>$product_updated['id'] ,
                    // 'account_id'=>$account_user->account_id ,
                    // 'user_id'=> $account_user->user_id,
                ]);    
                return $product_offer;
            }else{
                // exist check
                if($in_array->product_offer_statut==0){
                    $product = products_offer::where('id',$in_array->product_offer_id)->update(['statut'=>1]);
                
                return $in_array;
            }}
            
        });
        $desactive_offer = collect($product_offers)->map(function($product_offer) use($request_offers){
            $in_array = in_array($product_offer->offer_id, $request_offers);
            if($in_array == false){
                // exist
                $product = products_offer::where('id',$product_offer->product_offer_id)->update(['statut'=>0]);
        }});

        // change categories product
        $product_categories = DB::table('accounts_products')
            ->join('products', 'products.id', '=', 'accounts_products.product_id')
            ->join('categories', function ($join) use($id) {
                $join->on('categories.id', '=', 'accounts_products.category_id')
                    ->where('accounts_products.product_id', '=', $id);
            })
            ->select('categories.id as category_id',
                    'products.id as product_id',
                    'accounts_products.id as product_category_id',
                    'accounts_products.statut as product_category_statut',
            )->get();
        $request_categories = $request->input('categories');
        $reactive_or_add_category = collect($request_categories)->map(function($request_category) use($product_categories,$account_user,$product_updated){
            $in_array = collect($product_categories->whereIn('category_id', [$request_category]))->first();
            if($in_array == null){
                //not exist
                $product_category = accounts_products::create([
                    'category_id'=>$request_category,
                    'product_id'=>$product_updated['id'] ,
                    'account_id'=>$account_user->account_id ,
                    'user_id'=> $account_user->user_id,
                ]);    
                return $product_category;
            }else{
                // exist check
                if($in_array->product_category_statut==0){
                    // accounts_products::find($in_array->product_category_id)->update(['statut'=>1]);
                    $product = accounts_products::where('id',$in_array->product_category_id)->update(['statut'=>1]);
                
                return $in_array;
            }}
            
        });
        $desactive_category = collect($product_categories)->map(function($product_category) use($request_categories){
            $in_array = in_array($product_category->category_id, $request_categories);
            if($in_array == false){
                // exist
                $product = accounts_products::where('id',$product_category->product_category_id)->update(['statut'=>0]);
        }});

        // change suppliers product
        $product_suppliers = DB::table('products_suppliers')
            ->join('products', 'products.id', '=', 'products_suppliers.product_id')
            ->join('suppliers', function ($join) use($id) {
                $join->on('suppliers.id', '=', 'products_suppliers.supplier_id')
                    ->where('products_suppliers.product_id', '=', $id);
            })
            ->select('suppliers.id as supplier_id',
                    'products.id as product_id',
                    'products_suppliers.id as product_supplier_id',
                    'products_suppliers.statut as product_supplier_statut',
            )->get();
        $request_suppliers = $request->input('suppliers');
        $reactive_or_add_supplier = collect($request_suppliers)->map(function($request_supplier) use($product_suppliers,$account_user,$product_updated){
            $in_array = collect($product_suppliers->whereIn('supplier_id', [$request_supplier]))->first();
            if($in_array == null){
                //not exist
                $product_supplier = products_supplier::create([
                    'supplier_id'=>$request_supplier,
                    'product_id'=>$product_updated['id'] ,
                    // 'account_id'=>$account_user->account_id ,
                    // 'user_id'=> $account_user->user_id,
                ]);    
                return $product_supplier;
            }else{
                // exist check
                if($in_array->product_supplier_statut==0){
                    // products_supplier::find($in_array->product_supplier_id)->update(['statut'=>1]);
                    $product = products_supplier::where('id',$in_array->product_supplier_id)->update(['statut'=>1]);
                
                return $in_array;
            }}
            
        });
        $desactive_supplier = collect($product_suppliers)->map(function($product_supplier) use($request_suppliers){
            $in_array = in_array($product_supplier->supplier_id, $request_suppliers);
            if($in_array == false){
                // exist
                $product = products_supplier::where('id',$product_supplier->product_supplier_id)->update(['statut'=>0]);
        }});

        
        return response()->json([
            'product_size_request' => $request->input('offers'),
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
}
