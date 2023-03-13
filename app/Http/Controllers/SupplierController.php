<?php

namespace App\Http\Controllers;
use App\Http\Controllers\PhoneController;
use App\Http\Controllers\AddressController;
use Illuminate\Http\Request;
use App\Models\supplier;
use App\Models\account;
use App\Models\phone_type;
use App\Models\city;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelperFunctions;

class SupplierController extends Controller
{

    public static function index(Request $request, $local=0, $columns=['id', 'title', 'addresses', 'phones', 'products'], $paginate=true)
    {
        $filters = HelperFunctions::filterColumns($request->toArray(), ['title', 'addresse', 'phone', 'products']);
        
        $account = User::find(Auth::user()->id)->accounts->first();
        $suppliers = account::with(['suppliers'=>function($query) use($filters){
            if($filters['search'] == null){
                if($filters['filters']['title'] != null){
                    $query ->where('title', 'like', "%{$filters['filters']['title']}%" );
                }
            }else{
                $query->where('title', 'like', "%{$filters['search']}%" );
            }
            $query->with('activeProducts');
            $query->with(['addresses'=> function($query) use($filters){
                if($filters['filters']['addresse'] != null){
                    $query->Where('title', 'like', "%{$filters['filters']['addresse']}%" );
                }
            }]);
            $query->with(['phones'=> function($query) use($filters){
                if($filters['filters']['phone'] != null){
                    $query->Where('title', 'like', "%{$filters['filters']['phone']}%" );
                }
            }]);
        }])->find($account->id)->suppliers
            ->map(function($supplier) use($filters, $columns){
                $supplier->price = 0;
                $supplier->products = $products = $supplier->activeProducts->map(function($product){
                        $supplier_price =$product->pivot->price;
                        return ['id'=>$product->id, 'title' => $product->title, 'price' => $supplier_price];
                });
                   if($supplier->id == 7){
                        dd( $products->firstWhere('id', $filters['filters']['products'][0]), $products);
                    }      $productsExisting = HelperFunctions::filterExisting($filters['filters']['products'], $products->pluck('id'));
 
                if($productsExisting== true and count($filters['filters']['products'])>0){

                    $supplier->price = $products->firstWhere('id', $filters['filters']['products'][0])['price'];
                }
                $supplier->addresses = $addresses = $supplier->addresses->map(function($addresse){
                    return $addresse->only('id', 'title');
                });
                $supplier->phones = $phones = $supplier->phones->map(function($phone){
                    return $phone->only('id', 'title');
                });

                $add = $filters['filters']['addresse'] != null ?  $addresses->count() > 0 ? true : false : true;
                $pho = $filters['filters']['phone'] != null ?  $phones->count() > 0 ? true : false : true;
                $pro = $filters['filters']['products'] != null ?  $products->count() > 0 ? true : false : true;
                if($add and $pho and $productsExisting){
                    return $supplier->only($columns);
                }
            })->filter()->values();

            $dataPagination = HelperFunctions::getPagination($suppliers, $filters['pagination']['per_page'], $filters['pagination']['current_page']);
            if($local == 1){
                if($paginate == true){
                    return $dataPagination;
                }else{
                    return $suppliers->toArray();
                }
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
        $validator = Validator::make($request->all(),[
            'title' => 'required',
            'statut' => 'required',
            'phone_type_id' => 'required',
            'phone' => 'required',
            'account_city_id' => 'required',
            'address' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error'=> $validator->errors()
            ]);
        };
        $account = User::find(Auth::user()->id)->accounts->first();
        $account = account::find($account->id);
        $supplier = $account->suppliers()->create($request->all());
        $request_phone = new Request($request->only('phone', 'phone_type_id'));
        $phone = PhoneController::store( $request_phone, $local=1, $supplier);
        $request_address = new Request($request->only('address', 'account_city_id'));
        $phone = AddressController::store( $request_address, $local=1, $supplier);
        return response()->json([
            'statut' => 1,
            'data' => $phone 
        ]);
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $supplier = supplier::find($id);

        $cities = city::get();
        $phone_types = phone_type::get();

        return response()->json([
            'statut' => 1,
            'cities' => $cities,
            'phone_types' => $phone_types,
            'supplier' => $supplier
        ]);
    }

    public function update(Request $request, $id)
    {
        $account = User::find(Auth::user()->id)->accounts->first();
        $validator = Validator::make(collect($request->all())->put('id',$id)->all(), [
            'title' => '',
            'phone' => '',
            'phone_id' => 'required',
            'address' => 'required',
            'address_id' => 'required',
            'statut' => '',
            'id'=>'exists:suppliers,id,account_id,'.$account->id,
        ]);

        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        }

        $account = account::find($account->id);
        $supplier = supplier::find($id)->update($request->all());
        $supplier_up = supplier::find($id);
        $supplier_up->phone = PhoneController::update(new Request($request->only('phone', 'phone_id')), $request->phone_id, $local=1);
        $supplier_up->address = AddressController::update(new Request($request->only('address', 'address_id')), $request->address_id, $local=1);


        return response()->json([
            'statut' => 1,
            'data' => $supplier_up

        ]);
    }

    public function destroy($id)
    {
        $supplier_b =  supplier::find($id);
        $supplier = supplier::find($id)->delete();
        return response()->json([
            'statut' => 'deleted successfuly',
            'supplier' => $supplier_b,
        ]);
    }
}
