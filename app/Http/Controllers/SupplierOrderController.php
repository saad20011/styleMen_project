<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\accounts_carrier;
use App\Models\account_user;
use App\Models\delivery_men;
use App\Models\supplier_order;
use App\Models\supplier_order_product_size;
use Validator;
use DB;
use Auth;
class SupplierOrderController extends Controller
{

    public function index(Request $request)
    {
        $account_user = account_user::where('user_id',Auth::user()->id)
            ->first(['account_id','user_id']);
        $supplier_orders = supplier_order::where('account_id', $account_user->account_id)
            ->get();

        return response()->json([
            'statut' => 1,
            'delivery_man '=> $supplier_orders,
        ]);
    }

    public function create(Request $request)
    {
        $account_user = account_user::where('user_id',Auth::user()->id)
            ->first(['account_id','user_id']);
        $product_sizes = DB::table('suppliers')
            ->join('products_suppliers', function ($join) use($account_user,$request){
                $join->on('products_suppliers.supplier_id', '=', 'suppliers.id')
                    ->where('suppliers.account_id', '=', $account_user->account_id)
                    ->where('suppliers.id', '=', $request['supplier_id']); 
            })
            ->join('products', function($join) use($account_user){ 
                $join->on('products.id', '=', 'products_suppliers.product_id')
                ->where('products.account_id', '=', $account_user->account_id);
            })
            ->join('products_sizes',function($join){
                $join->on('products_sizes.product_id', '=', 'products.id')
                    ->where('products_sizes.statut', '1');
            })
            ->join('sizes', 'sizes.id', '=', 'products_sizes.size_id')
            ->select('suppliers.title as supplier_name',
                    'suppliers.account_id as supplier_account_id',
                    'products_sizes.id as product_size_id',
                    'products.title as product_name',
                    'products.id as product_id',
                    'sizes.title as sizes',
            )->get();
        $distinc = array();
            foreach($product_sizes as $key=>$value){
                if(array_key_exists($product_sizes[$key]->product_id, $distinc)==false){
                    $element = array(
                        "product_id"=>$product_sizes[$key]->product_id,
                        "supplier_name"=>$product_sizes[$key]->supplier_name,
                        "product_name"=>$product_sizes[$key]->product_name,
                        "sizes"=>array(
                            ["product_size_id" => $product_sizes[$key]->product_size_id,
                            "size"  =>  $product_sizes[$key]->sizes
                            ]
                            )
                        );
                    $distinc[$product_sizes[$key]->product_id]= $element;
                }else{
                    $in_array = collect($distinc[$product_sizes[$key]->product_id]["sizes"])->whereIn('size', [$product_sizes[$key]->sizes])->all();
                    if($in_array == [] ){
                            array_push($distinc[$product_sizes[$key]->product_id]["sizes"],
                                array(
                                    "product_size_id" => $product_sizes[$key]->product_size_id,
                                    "size"  =>  $product_sizes[$key]->sizes
                                )
                                );
                    }
                }
                } 
        return response()->json([
            'statut' => 1,
            'product_sizes ' => array_values($distinc),
        ]);
    }

    public function store(Request $request)
    {
        $without_first = collect($request->all())->forget(0)->all();
        $first = collect($request->all())->first();

        $validator = Validator::make($first, [
            'supplier_id' => 'required',
            'shipping_date' => 'required',
            'status' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error invoice', $validator->errors()
            ]);       
        };
        $validator = Validator::make($without_first, [
            '*.product_size_id' => 'required',
            '*.quantity' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error product_size', $validator->errors()
            ]);       
        };

        $account_user = account_user::where('user_id',Auth::user()->id)
        ->first(['account_id','user_id']);
        
        $supplier_order = supplier_order::create(
            [
                'reference' => 'SUP-'.substr(date("Ymdhi"), 2),
                'shipping_date' => $first['shipping_date'],
                'supplier_id' => $first['supplier_id'],
                'account_id' => $account_user->account_id,
                'user_id' => $account_user->user_id,
                'status' => $first['status']
            ]
        );
        $products_sizes_order = collect($without_first)->map(function($element) use($account_user,$supplier_order, $first){
            $product_supplier = DB::table('products_sizes')
                ->join('products_suppliers', function($join) use($element, $first){
                    $join->on('products_sizes.product_id', '=', 'products_suppliers.product_id')
                        ->where('products_sizes.id',$element['product_size_id'])
                        ->where('products_suppliers.supplier_id',$first['supplier_id']);
                })
                ->select('products_suppliers.price as price',
                        'products_sizes.id'
                )
                ->first();
            $product_size_order_only = collect($element)->only('product_size_id','quantity')
            ->put('user_id', $account_user->user_id)
            ->put('supplier_order_id', $supplier_order['id'])
            ->put('price', $product_supplier->price)
            ->all();
            $product_size_order = supplier_order_product_size::create($product_size_order_only);      
            return $product_size_order;      
        });

    
        return response()->json([
            'statut' => 1,
            'supplier order ' => $supplier_order,
            'products_sizes_order' => array_values($products_sizes_order->all())
        ]);
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $account_user = account_user::where('user_id',Auth::user()->id)
            ->first(['account_id','user_id']);

        $product_sizes = DB::table('supplier_orders')
            ->join('products_suppliers', function ($join) use($account_user, $id){
                $join->on('products_suppliers.supplier_id', '=', 'supplier_orders.supplier_id')
                    ->where('supplier_orders.account_id', '=', $account_user->account_id)
                    ->where('supplier_orders.id', '=', $id);
            })
            ->join('products', function($join) use($account_user){ 
                $join->on('products.id', '=', 'products_suppliers.product_id')
                ->where('products.account_id', '=', $account_user->account_id);
            })
            ->join('products_sizes',function($join){
                $join->on('products_sizes.product_id', '=', 'products.id')
                    ->where('products_sizes.statut', '1');
            })
            ->join('sizes', 'sizes.id', '=', 'products_sizes.size_id')
            ->leftJoin('supplier_order_product_sizes', function($join) use($id){
                $join->on('supplier_order_product_sizes.product_size_id', '=', 'products_sizes.id')
                ->where('supplier_order_product_sizes.supplier_order_id', '=', $id);

            })
            ->select(//'suppliers.title as supplier_name',
                    //'suppliers.account_id as supplier_account_id',
                    'products_sizes.id as product_size_id',
                    'products.title as product_name',
                    'products.id as product_id',
                    'sizes.title as sizes',
                    'supplier_order_product_sizes.quantity as product_size_quantity',
                    'supplier_order_product_sizes.price as product_size_price',
            )->get();
            // dd($product_sizes);
        $distinc = array();
            foreach($product_sizes as $key=>$value){
                if(array_key_exists($product_sizes[$key]->product_id, $distinc)==false){
                    $element = array(
                        "product_id"=>$product_sizes[$key]->product_id,
                        "product_name"=>$product_sizes[$key]->product_name,
                        "sizes"=>array(
                            ["product_size_id" => $product_sizes[$key]->product_size_id,
                            "product_size_quantity"=>$product_sizes[$key]->product_size_quantity,
                            "product_size_price"=>$product_sizes[$key]->product_size_price,                            
                            "size"  =>  $product_sizes[$key]->sizes
                            ]
                        )
                    );
                    $distinc[$product_sizes[$key]->product_id]= $element;
                }else{
                    $in_array = collect($distinc[$product_sizes[$key]->product_id]["sizes"])->whereIn('size', [$product_sizes[$key]->sizes])->all();
                    if($in_array == [] ){
                            array_push($distinc[$product_sizes[$key]->product_id]["sizes"],
                                array(
                                    "product_size_id" => $product_sizes[$key]->product_size_id,
                                    "product_size_quantity"=>$product_sizes[$key]->product_size_quantity,
                                    "product_size_price"=>$product_sizes[$key]->product_size_price,                            
                                    "size"  =>  $product_sizes[$key]->sizes
                                )
                                );
                    }
                }
                }
           return response()->json([
            'statut' => 1,
            'delivery_men ' => array_values($distinc),
            // 'carriers ' => $carriers
        ]);

    }

    public function update(Request $request, $id)
    {
        $without_first = collect($request->input())->forget(0)->all();
        $first = collect($request->input())->first();

        $validator = Validator::make($first, [
            'supplier_id' => 'required',
            'shipping_date' => 'required',
            'status' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error invoice', $validator->errors()
            ]);       
        };
        $validator = Validator::make($without_first, [
            '*.product_size_id' => 'required',
            '*.quantity' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error product_size', $validator->errors()
            ]);       
        };

        $account_user = account_user::where('user_id',Auth::user()->id)
            ->first(['account_id','user_id']);

        // change sizes product
        $product_sizes = DB::table('supplier_orders')
            ->join('supplier_order_product_sizes', function($join) use($account_user, $id){
                $join->on('supplier_order_product_sizes.supplier_order_id', '=', 'supplier_orders.id')
                    ->where('supplier_orders.account_id', '=', $account_user->account_id)
                    ->where('supplier_orders.id', '=', $id);
            })
            ->join('products_sizes', 'products_sizes.id', '=', 'supplier_order_product_sizes.product_size_id')
            ->join('products', 'products.id', '=', 'products_sizes.product_id')
            ->select(
            'supplier_order_product_sizes.product_size_id as product_size_id',
            'supplier_orders.id as supplier_orders_id',
            'supplier_order_product_sizes.quantity as product_size_quantity',
            'supplier_order_product_sizes.price as product_size_price',
            'supplier_order_product_sizes.id as supplier_order_product_size', 
            'products.price as product_price', 
            )->get();

        $supplier_order_product_size = collect($without_first)->map(function($product_size) use($product_sizes, $account_user, $id, $first){
            $in_array = collect($product_sizes->whereIn('product_size_id', [$product_size['product_size_id']]))->first();
            if($in_array == null){
                //not exist
                $product_supplier = DB::table('products_sizes')
                ->join('products_suppliers', function($join) use($product_size, $first){
                    $join->on('products_sizes.product_id', '=', 'products_suppliers.product_id')
                        ->where('products_sizes.id',$product_size['product_size_id'])
                        ->where('products_suppliers.supplier_id',$first['supplier_id']);
                })
                ->select('products_suppliers.price as price',
                        'products_sizes.id'
                )
                ->first();
                $product_size_only = collect($product_size)->only('product_size_id','quantity')
                ->put('user_id', $account_user->user_id)
                ->put('supplier_order_id', $id)
                ->put('price', $product_supplier->price)
                ->all();
                $product_size_order = supplier_order_product_size::create($product_size_only);
                return $product_size_order;
            }else{
                // exist check
                $supp_order_product_size_upd = supplier_order_product_size::where('id',$in_array->supplier_order_product_size)->update(['quantity'=>$product_size['quantity']]);
                return $supp_order_product_size_upd;
            }
            
        });

        return response()->json([
            'statut' => 1,
            'delivery_men' => $supplier_order_product_size,
        ]);
    }


    public function destroy($id)
    {
        $delivery_men_b =  delivery_men::find($id);
        $delivery_men = delivery_men::find($id)->delete();
        return response()->json([
            'statut' => 1,
            'delivery_men' => $delivery_men_b,
        ]);
    }
}
