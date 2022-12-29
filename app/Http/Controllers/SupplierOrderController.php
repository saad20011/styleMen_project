<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\account_user;
use App\Models\account;
use App\Models\product_variationAttribute;
use App\Models\User;
use App\Models\product_supplier;
use App\Models\supplier_order;
use App\Models\supplier_order_product_variationAttribute;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class SupplierOrderController extends Controller
{

    public function index(Request $request)
    {
        $account_user = User::find(Auth::user()->id)->account_user->first();
        $accounts_users = account::find($account_user->account_id)->account_user->pluck('id')->toArray();
        $supplier_order_product_variationAttribute= account_user::with(['supplier_orders'=>function($query) use($request){
            $query->with(['suppliers' => function($query){
                $query->with('phones');
            }]);
        }])->whereIn('id', $accounts_users)->first();

        return response()->json([
            'statut' => 1,
            'data' => $supplier_order_product_variationAttribute
        ]);
    }

    public function create(Request $request)
    {
        $account = User::find(Auth::user()->id)->accounts->first();
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,id,account_id,'.$account->id,
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error invoice', $validator->errors()
            ]);       
        };
        $account = User::find(Auth::user()->id)->accounts->first();
        $suppliers = Supplier::where(['id' => $request->supplier_id , 'account_id' => $account->id])
            ->with(['products' => function($query) {
                $query->with(['variationAttributes' => function($query) {
                    $query->with('attributes');
                }]);
        }])->first();

        return response()->json([
            'statut' => 1,
            'data' => $suppliers,
        ]);
    }

    public function store(Request $request)
    {
        $account_user = User::find(Auth::user()->id)->account_user->first();
        $accounts_users = account::find($account_user->account_id)->account_user->pluck('id')->toArray();
        
        $products= account::with(['products'=>function($query) use($request){
            $query->with(['product_variationAttribute' => function($query){
                // $query->where('product_variationAttribute.statut', 1);
            }, 'product_supplier' => function($query)use($request){
                $query->where('product_supplier.supplier_id', $request->supplier_id);
            }]);
        }])->whereIn('id',$accounts_users)->first()->products;
        $product_variationAttributes = collect($products)->map(function($product){
            if(count($product->product_supplier) >=1)
                return $product->product_variationAttribute;
        })->collapse();
        // dd($product_variationAttributes->toArray());

        Validator::extend('even', function ($attribute, $value)use($product_variationAttributes) {
            return $product_variationAttributes->contains('id',$value);
        });

        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,id,account_id,'.$account_user->id,
            'shipping_date' => 'required',
            'product_variationAttribute.*.product_variationAttribute_id' => 'required|even',
            'product_variationAttribute.*.quantity' => 'required|gt:0',
            'statut' => 'required|in:0,1',
        ],$messages = [
            'even' => 'this supplier_id : '.$request->supplier_id.' donsn\'t have this product_variationAttribute : :input ',
        ]);
        
        if($validator->fails()){
            return response()->json([
                'Validation Error invoice', $validator->errors()
            ]);       
        };

        $supplier_order = supplier_order::create(
            [
                'code' => 'SUP-'.substr(date("Ymdhi"), 2),
                'shipping_date' => $request->shipping_date,
                'supplier_id' => $request->supplier_id,
                'account_user_id' => $account_user->id,
                'statut' => $request->statut 
            ]
        );
            // dd( $request->product_variationAttribute );
        $supplier_order->products_sizes_order = collect($request->product_variationAttribute)->map(function($element) use($account_user,$supplier_order, $request){
            $product_variationAttribute = product_variationAttribute::find($element['product_variationAttribute_id']);
            $product_price = product_supplier::where(['product_id'=>$product_variationAttribute->product_id, 'supplier_id' => $request->supplier_id])->first()->price;
            $product_variationAttribute_order_only = collect($element)->only('product_variationAttribute_id','quantity')
                ->put('user_id', $account_user->user_id)
                ->put('supplier_order_id', $supplier_order['id'])
                ->put('price', $product_price)
                ->put('statut' ,  $request->statut == 1 ? 1 : 2)
                ->all();
            
            $product_variationAttribute_order = supplier_order_product_variationAttribute::create($product_variationAttribute_order_only);
            return $product_variationAttribute_order->toArray();      
        })->values()->all();

        return response()->json([
            'statut' => 1,
            'data' => $supplier_order,
        ]);
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $account_user = User::find(Auth::user()->id)->account_user->first();
        $accounts_users = account::find($account_user->account_id)->account_user->pluck('id')->toArray();
        $validator = Validator::make(['id' => $id], [
            'id' => 'exists:supplier_orders,id,account_user_id,'.$account_user->id,
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error invoice', $validator->errors()
            ]);       
        };
        $supplier_order = supplier_order::find($id);
        if($supplier_order->statut == 2){
            return response()->json([
                'statut' => 0,
                'data'=> 'déja validé, vous n\'avez pas le droit'
            ]);
        } 
        $products= account::with(['products'=>function($query) use($id){
            $query->with(['product_variationAttribute' => function($query) use($id){
                // $query->where('product_variationAttribute.statut', 1);
                $query->with(['supplier_order_product_variationAttribute'=> function($query) use($id){
                    $query->where(['supplier_order_product_variationAttribute.supplier_order_id'=> $id ])
                        ->whereIn('statut',[1,2])
                    ;
                }]);
            },'principal_images'])->simplePaginate(10, ['*'], 'page', 1);
        }])->whereIn('id',$accounts_users)->first()->products;
        return response()->json([
            'statut' => 1,
            'data' => $products
        ]);
    }

    public function update(Request $request, $id)
    {
        $account_user = User::find(Auth::user()->id)->account_user->first();
        $accounts_users = account::find($account_user->account_id)->account_user->pluck('id')->toArray();
        $products= account::with(['products'=>function($query) use($id, $request){
            $query->with(['product_variationAttribute' => function($query) use($id){
                // $query->where('product_variationAttribute.statut', 1);
                    $query->with(['supplier_order_product_variationAttribute' => function($query) use($id){
                        $query->where('supplier_order_product_variationAttribute.supplier_order_id', $id);
                    } ]);
            }, 'product_supplier' => function($query)use($request){
                $query->where('product_supplier.supplier_id', $request->supplier_id);
            }]);
        }])->whereIn('id',$accounts_users)->first()->products;
        $product_variationAttributes = collect($products)->map(function($product){
            if(count($product->product_supplier) >=1)
                return $product->product_variationAttribute;
        })->collapse();
        Validator::extend('even', function ($attribute, $value)use($product_variationAttributes) {
            return $product_variationAttributes->contains('id',$value);
        });

        $validator = Validator::make(collect($request->all())->put('id',$id)->all(), [
            'id' => 'exists:supplier_orders,id,account_user_id,'.$account_user->id,
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error id', $validator->errors()
            ]);       
        };
        $supplier_order = supplier_order::find($id);
        if($supplier_order->statut == 1){
            return response()->json([
                'statut' => 0,
                'data'=> 'déja validé, vous n\'avez pas le droit'
            ]);
        } 

        if(isset($request->statut)){
            if($request->statut == 2){
                supplier_order::find($id)->update(['statut' => 1]);
                supplier_order_product_variationAttribute::where(['supplier_order_id'=>$id, 'statut' => 2])->update(['statut'=> 1]);
                return response()->json([
                    'statut' => 1,
                    'data'=> 'bien validé'
                ]);
        }}
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'exists:suppliers,id,account_id,'.$account_user->id,
            'shipping_date' => 'date',
            'product_variationAttribute.*.product_variationAttribute_id' => 'required|even',
            'product_variationAttribute.*.quantity' => 'required|gt:5',
            'statut' => 'in:0,1,2'
        ],$messages = [
            'even' => 'this supplier_id : '.$request->supplier_id.' donsn\'t have this product_variationAttribute : :input ',
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error invoice', $validator->errors()
            ]);       
        };

        $supplier_order->update($request->only('supplier_id', 'shipping_date','statut'));
        $supplier_order_updated = supplier_order::find($id);

        $request_product_variationAttribute = $request->product_variationAttribute;
        $products_sizes_updated = collect($product_variationAttributes)->map(function($element) use($id, $request_product_variationAttribute,$request, $account_user){
            if(count($element->supplier_order_product_variationAttribute) >0){
                if(collect($request_product_variationAttribute)->contains('product_variationAttribute_id', $element->id)){
                    $product_variationAttribute_updated = supplier_order_product_variationAttribute::find($element->supplier_order_product_variationAttribute->first()->id)
                        ->update(['statut' => $request->statut == 1 ? 1 : 2, 'quantity' => collect($request_product_variationAttribute)->firstWhere('product_variationAttribute_id', $element->id)['quantity']]);
                        return collect($request_product_variationAttribute)->where('product_variationAttribute_id', $element->id)->first() ;
                        
                }else{
                    $product_variationAttribute_updated = supplier_order_product_variationAttribute::find($element->supplier_order_product_variationAttribute->first()->id)
                        ->update(['statut' => 0]); 
                        return collect($request_product_variationAttribute)->firstWhere('product_variationAttribute_id', $element->id) ;
                }
            }else{
                if(collect($request_product_variationAttribute)->contains('product_variationAttribute_id', $element->id)){
                    $product_price = product_supplier::where(['product_id' => $element->product_id, 'supplier_id' => $request->supplier_id])
                                    ->first()->price;
                    $product_variationAttribute_order_only = collect(collect($request_product_variationAttribute)->firstWhere('product_variationAttribute_id', $element->id))->only('product_variationAttribute_id','quantity')
                        ->put('user_id', $account_user->user_id)
                        ->put('supplier_order_id', $id)
                        ->put('price', $product_price)
                        ->put('statut', $request->statut == 1 ? 1 : 2)
                        ->all();
                    $product_variationAttribute_order = supplier_order_product_variationAttribute::create($product_variationAttribute_order_only);    
                    return $product_variationAttribute_order;        
                }
            }
        })->filter()->values()->all();

        return response()->json([
            'statut' => 1,
            'porducts_sizes_updated' => $products_sizes_updated
        ]);
    }


    public function destroy($id)
    {
    //
    }
}
