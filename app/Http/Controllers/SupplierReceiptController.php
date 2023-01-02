<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\account_user;
use App\Models\account;
use App\Models\User;
use App\Models\supplier_receipt;
use App\Models\product_supplier;
use App\Models\supplier_order_product_variationAttribute;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SupplierReceiptController extends Controller
{
    public function index(Request $request){
        $account_user = User::find(Auth::user()->id)->account_user->first();
        $accounts_users = account::find($account_user->account_id)->account_user->pluck('id')->toArray();

        $supplier_order_product_variationAttribute= account_user::with(['supplier_receipts'=>function($query) use($request){
            $query->with('suppliers');
        }])->whereIn('id', $accounts_users)->first();


        return response()->json([
            'statut' => 1,
            'data' => $supplier_order_product_variationAttribute
        ]);
    }

    public function create(Request $request){
        //ghadi nsift ga3 les product_variationAttribute_id libaqi en attente wm3ahom nom et les size en attente des produits 
        $account_user = User::find(Auth::user()->id)->account_user->first();
        $accounts_users = account::find($account_user->account_id)->account_user->pluck('id')->toArray();
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,id,account_id,'.$account_user->id,
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error invoice', $validator->errors()
            ]);       
        };
        
        $supplier_orders= account_user::with(['supplier_orders'=>function($query) use($request){
            $query->where('supplier_orders.supplier_id', $request->supplier_id)
                ->with(['supplier_order_product_variationAttribute' => function($query){
                    $query->where('supplier_order_product_variationAttribute.quantity', '>', 0)
                            ->with(['product_variationAttributes' => function($query){
                    $query->with(['products' => function($query){
                        $query->with('images');
                    }]);
                }]);
            }]);
        }])->whereIn('id', $accounts_users)->first()->supplier_orders;

        $supplier_order_product_variationAttribute = collect($supplier_orders)->map(function($supplier_order){
            return $supplier_order->supplier_order_product_variationAttribute;
        })->collapse()->sortby('created_at')->unique('product_variationAttribute_id')->values()->toArray();
        return response()->json([
            'statut' => 1,
            'data' => $supplier_order_product_variationAttribute
        ]);
    }

    public function store(Request $request)
    {    
        $account_user = User::find(Auth::user()->id)->account_user->first();
        $accounts_users = account::find($account_user->account_id)->account_user->pluck('id')->toArray();

        $supplier_orders= account_user::with(['supplier_orders'=>function($query) use($request){
            $query->where(['supplier_orders.supplier_id' => $request->supplier_id , 'supplier_orders.statut' => 1])
                ->with(['supplier_order_product_variationAttribute' => function($query){
                $query->where('supplier_order_product_variationAttribute.quantity', '>', 0)
                        ->where('supplier_order_product_variationAttribute.statut' , 1);
            }]);
        }])->whereIn('id', $accounts_users)->first()->supplier_orders;
        $supplier_order_product_variationAttribute = collect($supplier_orders)->map(function($supplier_order){
            return $supplier_order->supplier_order_product_variationAttribute;
        })->collapse()->sortby('created_at');
        // dd($supplier_order_product_variationAttribute->toArray());
        Validator::extend('even', function ($attribute, $value) use($supplier_order_product_variationAttribute) {
            if($supplier_order_product_variationAttribute->contains('product_variationattribute_id',$value['product_variationAttribute_id'])){
                if($supplier_order_product_variationAttribute->where('product_variationattribute_id',$value['product_variationAttribute_id'])->sum('quantity') - $value['quantity'] >= 0)
                return true;
            }
                return false;
        });

        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,id,account_id,'.$account_user->id,
            'shipping_date' => 'required',
            'statut' => 'required|in:0,1',
            'product_variationAttribute.*' => 'required|even',
            'product_variationAttribute.*.quantity' => 'required|gt:0',
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error invoice', $validator->errors()
            ]);       
        };

        $supplier_receipt = supplier_receipt::create(
            [
                'code' => 'SUP-'.substr(date("Ymdhi"), 2),
                'supplier_id' => $request->supplier_id,
                'account_user_id' => $account_user->id,
                'statut' => $request->statut,
            ]
        );
        $supplier_receipt->products_sizes_order = collect($request->product_variationAttribute)->map(function($element) use($account_user,$supplier_receipt, $supplier_order_product_variationAttribute, $request){
            $supplier_product_variationAttribute = $supplier_order_product_variationAttribute->where('product_variationattribute_id', $element['product_variationAttribute_id']);
            // dd($element['product_variationAttribute_id'],$supplier_product_variationAttribute->toAR);
            $qunatity = $element['quantity'];
            if($request->statut == 1){
                foreach ($supplier_product_variationAttribute as $item) {
                    // dd($supplier_product_variationAttribute->quantity);
                    // dd($item );
                    if ($qunatity - $item->quantity >= 0) {
                        supplier_order_product_variationAttribute::find($item->id)->update(['quantity'=> 0]);
                        $qunatity -= $item->quantity;
                    }else if($qunatity - $item->quantity < 0){
                        supplier_order_product_variationAttribute::find($item->id)->update(['quantity'=>  $item->quantity -  $qunatity]);
                        $qunatity -= $item->quantity;
                    }
                    if ($qunatity <= 0)
                        break;
                }
            }
            $product_variationAttribute_order_only = collect($element)->only('product_variationAttribute_id','quantity')
                ->put('user_id', $account_user->user_id)
                ->put('supplier_receipt_id', $supplier_receipt['id'])
                ->put('price', $supplier_product_variationAttribute->first()->price)
                ->put('statut', $request->statut == 0 ? 2 : 1)
                ->all(); 
            $product_variationAttribute_order = supplier_order_product_variationAttribute::create($product_variationAttribute_order_only);

            return $product_variationAttribute_order;      

        })->values()->all();

        return response()->json([
            'statut' => 1,
            'supplier order ' => $supplier_receipt,
        ]);
    }

    public function edit($id)
    { 
        $account_user = User::find(Auth::user()->id)->account_user->first();
        $accounts_users = account::find($account_user->account_id)->account_user->pluck('id')->toArray();
        $validator = Validator::make(['id' => $id], [
            'id' => 'exists:supplier_receipts,id,account_user_id,'.$account_user->id,
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error invoice', $validator->errors()
            ]);       
        };
        $supplier_order = supplier_receipt::find($id);
        if($supplier_order->statut == 1){
            return response()->json([
                'statut' => 0,
                'data'=> 'déja validé, vous n\'avez pas le droit'
            ]);
        } 
        $products= account::with(['products'=>function($query) use($id){
            $query->with(['product_variationAttribute' => function($query) use($id){
                // $query->where('product_variationAttribute.statut', 1);
                $query->with(['supplier_order_product_variationAttribute'=> function($query) use($id){
                    $query->where('supplier_order_product_variationAttribute.supplier_receipt_id', $id);
                }]);
            },'principal_images'])->simplePaginate(10, ['*'], 'page', 1);
        }])->whereIn('id',$accounts_users)->first();

        return response()->json([
            'statut' => 1,
            'supplier receipt' => $products
        ]);
    }

    public function update(Request $request, $id){

        $account_user = User::find(Auth::user()->id)->account_user->first();
        $account = User::find(Auth::user()->id)->accounts->first();
        $accounts_users = account::find($account_user->account_id)->account_user->pluck('id')->toArray();
        $products= account::with(['products'=>function($query) use($id, $request){
            $query->with(['product_variationAttribute' => function($query) use($id){
                //  $query->where('product_variationAttribute.statut', 1);
                    $query->with(['supplier_order_product_variationAttribute' => function($query) use($id){
                        $query->where('supplier_order_product_variationAttribute.supplier_receipt_id', $id);
                    } ]);
            }, 'product_supplier' => function($query) use($request){
                    $query->where('product_supplier.supplier_id', $request->supplier_id);
            }]);
        }])->where('id',$account->id)->first()->products;
        $product_variationAttributes = collect($products)->map(function($product){
            if(count($product->product_supplier) >=1 )
                return $product->product_variationAttribute;
        })->collapse();
        // dd($product_variationAttributes->toArray());
        $supplier_orders= account_user::with(['supplier_orders'=>function($query) use($request){
            $query->where('supplier_orders.supplier_id', $request->supplier_id)
                ->with(['supplier_order_product_variationAttribute' => function($query){
                $query->where('supplier_order_product_variationAttribute.quantity', '>', 0);
            }]);
        }])->whereIn('id', $accounts_users)->first()->supplier_orders;

        $supplier_product_variationAttribute = collect($supplier_orders)->map(function($supplier_order){
            return $supplier_order->supplier_order_product_variationAttribute;
        })->collapse()->sortby('created_at');
        // dd($supplier_product_variationAttribute);
        Validator::extend('even', function ($attribute, $value)use($product_variationAttributes, $supplier_product_variationAttribute) {
            if($product_variationAttributes->contains('id',$value['product_variationAttribute_id'])){
                if($supplier_product_variationAttribute->where('product_variationattribute_id',$value['product_variationAttribute_id'])->sum('quantity') - $value['quantity'] >= 0)
                    return true;
            }
            return false;
        });
        $validator = Validator::make(collect($request->all())->put('id',$id)->all(), [
            'id' => 'exists:supplier_receipts,id,account_user_id,'.$account_user->id,
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error id', $validator->errors()
            ]);       
        };
        $supplier_receipt = supplier_receipt::find($id);
        if($supplier_receipt->statut == 1){
            return response()->json([
                'statut' => 0,
                'data'=> 'déja validé, vous n\'avez pas le droit'
            ]);
        } 

        if(isset($request->statut)){
            if($request->statut == 2){
                supplier_receipt::find($id)->update(['statut' => 1]);
                supplier_order_product_variationAttribute::where(['supplier_receipt_id'=>$id, 'statut' => 2])->update('statut', 1);
                return response()->json([
                    'statut' => 1,
                    'data'=> 'bien validé'
                ]);
        }}
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'exists:suppliers,id,account_id,'.$account_user->id,
            'shipping_date' => 'date',
            'product_variationAttribute.*' => 'even',
            'product_variationAttribute.*.quantity' => 'required|gt:0',
            'statut' => 'required|in:0,1',
        ],$messages = [
            'even' => 'this supplier_id:'.$request->supplier_id.' donsn\'t have this product_variationAttribute : :input ',
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error invoice', $validator->errors()
            ]);       
        };

        $supplier_receipt->update($request->only('supplier_id', 'shipping_date'));
        $supplier_receipt_updated = supplier_receipt::find($id);
        $request_product_variationAttribute = $request->product_variationAttribute;
        $products_sizes_updated = $product_variationAttributes->map(function($element) use($id, $request_product_variationAttribute,$request, $account_user, $supplier_product_variationAttribute){
            //verifier si le product_variationAttribute a déja une supplier_receipt
            if(count($element->supplier_order_product_variationAttribute) >0){
                if(collect($request_product_variationAttribute)->contains('product_variationAttribute_id', $element->id)){
                    $element_request = collect($request_product_variationAttribute)->firstWhere('product_variationAttribute_id', $element->id);
                    //verifier si la validation 0 ou 1
                    if($request->statut == 0){
                        // si la vaildation est 0 on va pas dimunier la quantité du stock on va seulement changer la qunatité de la ligne supplier_order_product_variationAttribute qui est déja fait
                        $product_variationAttribute_updated = supplier_order_product_variationAttribute::find($element->supplier_order_product_variationAttribute->first()->id)
                            ->update(['statut' => 2, 'quantity' => $element_request['quantity']]);
                            return collect($request_product_variationAttribute)->where('product_variationAttribute_id', $element->id)->first() ;
                            
                    }else{
                        $quantity_rest = $element_request['quantity'];
                        // si la validation est 1 on va changer la qunatité du stock 
                        foreach (collect($supplier_product_variationAttribute)->where('product_variationattribute_id',$element->id) as $item) {
                            if ($quantity_rest - $item->quantity >= 0) {
                                supplier_order_product_variationAttribute::find($item->id)->update(['quantity'=> 0]);
                                $quantity_rest -= $item->quantity;
                            }else if($quantity_rest - $item->quantity < 0){
                                supplier_order_product_variationAttribute::find($item->id)->update(['quantity'=>  $item->quantity -  $quantity_rest]);
                                $quantity_rest -= $item->quantity;
                            }
                            if ($quantity_rest <= 0)
                                break;
                        }
                        $product_variationAttribute_updated = supplier_order_product_variationAttribute::find($element->supplier_order_product_variationAttribute->first()->id)
                            ->update(['statut' => 1, 'quantity' => $element_request['quantity']]);
                        supplier_receipt::find($id)->update(['statut' => 1]);
                        return $product_variationAttribute_updated;
                    }
                }else{
                    $product_variationAttribute_updated = supplier_order_product_variationAttribute::find($element->supplier_order_product_variationAttribute->first()->id)
                        ->update(['statut' => 0]); 
                        return collect($request_product_variationAttribute)->firstWhere('product_variationAttribute_id', $element->id) ;
                }
            }else{
                if(collect($request_product_variationAttribute)->contains('product_variationAttribute_id', $element->id)){
                    $product_price = product_supplier::where(['product_id' => $element->product_id, 'supplier_id' => $request->supplier_id])
                                    ->first()->price;
                    $product_variationAttribute_receipt_only = collect(collect($request_product_variationAttribute)->firstWhere('product_variationAttribute_id', $element->id))->only('product_variationAttribute_id','quantity')
                        ->put('user_id', $account_user->user_id)
                        ->put('supplier_receipt_id', $id)
                        ->put('price', $product_price)
                        ->put('statut', $request->statut == 0 ? 2 : 1)
                        ->all();
                    $product_variationAttribute_order = supplier_order_product_variationAttribute::create($product_variationAttribute_receipt_only);    
                    if($request->statut == 1){
                        $element_request = collect($request_product_variationAttribute)->firstWhere('product_variationAttribute_id', $element->id);
                        $quantity_rest = $element_request['quantity'];
                        foreach (collect($supplier_product_variationAttribute)->where('product_variationattribute_id',$element->id) as $item) {
                            if ($quantity_rest - $item->quantity >= 0) {
                                supplier_order_product_variationAttribute::find($item->id)->update(['quantity'=> 0]);
                                $quantity_rest -= $item->quantity;
                            }else if($quantity_rest - $item->quantity < 0){
                                supplier_order_product_variationAttribute::find($item->id)->update(['quantity'=>  $item->quantity -  $quantity_rest]);
                                $quantity_rest -= $item->quantity;
                            }
                            if ($quantity_rest <= 0)
                                break;
                        }
                        supplier_receipt::find($id)->update(['statut' => 1]);
                        return $product_variationAttribute_order; 
                    }
                    return $product_variationAttribute_order; 
                }
            }
        })->filter()->values()->toArray();

    return response()->json([
            'statut' => 1,
            'porducts_attributes_update' => $products_sizes_updated
        ]);
    }

    public function destroy($id){

    }
}
