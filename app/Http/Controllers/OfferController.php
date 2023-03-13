<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\offer;
use App\Models\product_offer;
use App\Models\User;
use App\Models\account;
use App\Models\account_user;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\HelperFunctions;

class OfferController extends Controller
{

    public static function index(Request $request, $local=0, $columns=['id', 'title', 'price', 'shipping_price', 'brands', 'products'], $paginate=true)
    {
        $filters = self::filterColumns($request->toArray(), ['title', 'price', 'shipping_price', 'brands', 'products']);
        $account = User::find(Auth::user()->id)->accounts->first();
        $brands = account::find($account->id)->brands->map(function($brand){
            return $brand->only('id', 'title');
        });
        $products = account::find($account->id)->products->map(function($product){
            return $product->only('id','reference');
        });
        $offers = account::with(['offers' => function($query) use($filters){
            $query->where('offers.offer_id',null);
                if($filters['search'] == null){
                    //filtering by columns
                    if($filters['filters']['title'] != null){
                        $query->where('title', 'like', "%{$filters['filters']['title']}%" );
                    }
                    if($filters['filters']['price'] != null){
                        $query->Where('price', 'like', "%{$filters['filters']['price']}%" );
                    }
                    if($filters['filters']['shipping_price'] != null){
                        $query->Where('shipping_price', 'like', "%{$filters['filters']['shipping_price']}%" );
                    }
                }else{
                    $query->where('title', 'like', "%{$filters['search']}%" )
                        ->orWhere('price', 'like', "%{$filters['search']}%" )
                        ->orWhere('shipping_price', 'like', "%{$filters['search']}%" );
                }
            // filtering date
            if($filters['startDate'] != null and $filters['startDate'] != null){
                $query->whereBetween('created_at', [$filters['startDate'], $filters['endDate']]);
            }   
                $query->with(['product_offer' => function($query){
                    $query->where('status',true)
                    ->with(['account_product' => function($query){
                            $query->with(['products' => function($query){
                                }]);
                            }]);
                    },'offers' => function($query){
                            $query->where('offers.statut', true)
                            ->with(['brands' => function($query){
                        }]);
                    }])->paginate();
        }])->find($account->id)->offers
            ->map(function ($offer) use($filters, $columns){
                $offer->brands = $brands =  $offer->offers->map(function ($nestedOffer) {
                    $brands = $nestedOffer->brands->map(function($brand){
                        return $brand->only('title', 'id') ;
                    });
                    return $brands;
                })->collapse();

                $offer->products = $products = $offer->product_offer->map(function($productOffer){
                    $products = $productOffer->account_product->products->map(function($product){
                        return $product->only('reference', 'id');
                    });
                    return $products;
                })->collapse();
        
                $productExisting = HelperFunctions::filterExisting($filters['filters']['products'], $products->pluck('id'));
                $brandExisting = HelperFunctions::filterExisting($filters['filters']['brands'], $brands->pluck('id'));

                if($productExisting == true and $brandExisting == true){
                    return collect($offer)->only($columns);
                }
        })->filter()->values();
        $dataPagination = self::getPagination($offers, $filters['pagination']['per_page'], $filters['pagination']['current_page']);
        if($local == 1){
            if($paginate == true){
                return $dataPagination;
            }else{
                return $offers->toArray();
            }
        };
        return response()->json(
            array_merge($dataPagination,
        ['brands'=>$brands->toArray(),'products'=>$products->toArray()]
        ));
    }

    public function create(Request $request)
    {
        $request = collect($request->query())->toArray();
        $offer= [];
        if (isset($request['brands']['inactive'])){ 
            $offer['brands']['inactive'] = BrandController::index(new Request($request['brands']['inactive']),$local=1,['id','title'], true);
        }
        
        if (isset($request['products']['inactive'])){ 
            $offer['products']['inactive'] = ProductController::index(new Request($request['products']['inactive']),$local=1,['id','title'], true,true);
        }
        return response()->json([
            'statut' => 1,
            'data' => $offer,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'title' => 'required|', //unique:offers
            // 'price' => 'required',
            // 'shipping_price' => 'required',
            // 'statut' => 'required',
            // 'brands' => 'required|array|min:1',
            // 'brands.*'=> 'required|exists:brands,id',
            // 'products' => 'required|array|min:1',
            // 'products.*'=> 'required|exists:products,id'
        ]);

        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };
        $account = User::find(Auth::user()->id)->accounts->first();
        $mother_offer = account::find($account->id)->offers()
                ->create($request->offer);
        if(isset($request['brands']['toActive']) and $request['brands']['toActive'] !=1){
            $mother_offer->brands = $this->update_brand_offers($account->id, $mother_offer->id,$request['brands']['toActive'],'toActive');        
        }
        if(isset($request['products']['toActive']) and $request['products']['toActive'] !=1){
            $mother_offer->products = $this->update_offer_product($account->id, $mother_offer->id,$request['products']['toActive'] ,'toActive');
        }
        return response()->json([
            'statut' => 1,
            'data' => $mother_offer,
        ]);

    }


    public function show($id)
    {
        //
    }

    public function edit(Request $request, $id)
    {
        $request = collect($request->query())->toArray();

        $account = User::find(Auth::user()->id)->accounts->first();
        $offer = account::with(['offers' => function($query) use($id){
            $query->where('offers.id', $id);
        }])->find($account->id)->offers->first()->only('id','title','price', 'shipping_price', 'statut');
        $offer = collect($offer)->toArray();
        
        if (isset($request['offer'])){ 
            $offer['offer']['data'] = $offer ;
        };//dd($request);
        if (isset($request['brands']['active'])){ 
            $offer['brands']['active'] = $this->brandsByOffer($id,$request['brands']['active'], true,true);
        };
        if (isset($request['brands']['inactive'])){ 
            $offer_brands_inactive = $this->brandsByOffer($id,$request['brands']['inactive'], false);
            $offer_all_brands = BrandController::index(new Request($request['brands']['inactive']),1,['id','title', 'website', 'email']);
            $unique = HelperFunctions::getInactiveData($offer_all_brands, $offer_brands_inactive);
            $offer['brands']['inactive'] = $this->getPagination(collect($unique), 
                isset($request['brands']['inactive']['pagination']['per_page'])== true ?$request['brands']['inactive']['pagination']['per_page'] : null,
                isset($request['brands']['inactive']['pagination']['current_page'])== true ?$request['brands']['inactive']['pagination']['current_page'] : null);
        };
        if (isset($request['products']['active'])){ 
            $offer['products']['active'] = $this->productsByOffer($id,$request['products']['active'], true,true);
        };
        if (isset($request['products']['inactive'])){ 
            $offer_products_inactive = $this->productsByOffer($id,$request['products']['inactive'], false,);
            $offer_all_products = ProductController::index(new Request($request['products']['inactive']),1,['id','title', 'website', 'email']);
            $unique = HelperFunctions::getInactiveData($offer_all_products, $offer_products_inactive);
            $offer['products']['inactive'] = $this->getPagination(collect($unique), 
                isset($request['products']['inactive']['pagination']['per_page'])== true ?$request['products']['inactive']['pagination']['per_page'] : null,
                isset($request['products']['inactive']['pagination']['current_page'])== true ?$request['products']['inactive']['pagination']['current_page'] : null);
        };

        return response()->json([
            'statut' => 1,
            'data' => $offer,
        ]);
    }

    public function update(Request $request, $id)
    {
        $account = User::find(Auth::user()->id)->accounts->first();
        $validator = Validator::make(collect($request)->put('offer_id', $id)->all(), [
            'offer.title' => '|', //unique:offers
            'offer.price' => '',
            'offer.shipping_price' => '',
            'offer_id' => 'required|exists:offers,id,account_id,'.$account->id.',offer_id,NULL',
            // 'brands.toActive' => 'array',
            // 'brands.toInactive' => 'array',
            // 'products.toActive' => 'array',
            // 'products.toInactive' => 'array',
            'brands.toActive.*'=> 'exists:brands,id,account_id,'.$account->id,
            'brands.toInactive.*'=> 'exists:brands,id,account_id,'.$account->id,
            'products.toActive.*'=> 'exists:account_product,id,account_id,'.$account->id,
            'products.toInactive.*'=> 'exists:account_product,id,account_id,'.$account->id,
        ]);

        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        }
        $request = $request->toArray();
        $offer_updated = [];
        if(isset($request['offer']) and $request['offer'] !=1){
            $offer = offer::find($id)->update($request['offer']);
            $offer_updated['offer'] = offer::find($id);        
        };
        if(isset($request['brands']['toActive']) and $request['brands']['toActive'] !=1){
            $offer_updated['brands']['actived'] = $this->update_brand_offers($account->id,$id,$request['brands']['toActive'], 'toActive');
        }
        if(isset($request['brands']['toInactive']) and $request['brands']['toInactive'] !=1){
            $offer_updated['brands']['inactived'] = $this->update_brand_offers($account->id,$id,$request['brands']['toInactive'], 'toInactive');
        }
        if(isset($request['products']['toActive']) and $request['products']['toActive'] !=1){
            $offer_updated['products']['actived']  = $this->update_offer_product($account->id,$id,$request['products']['toActive'],'toActive');
        }
        if(isset($request['products']['toInactive']) and $request['products']['toInactive'] !=1){
                $offer_updated['products']['inactived'] = $this->update_offer_product($account->id,$id,$request['products']['toInactive'],'toInactive');
        }

        return response()->json([
            'statut' => 1,
            'data' => $offer_updated,
        ]);
    }


    public function destroy($id)
    {
        $offer_b =  offer::find($id);
        $offer = offer::find($id)->delete();
        return response()->json([
            'statut' => 'deleted successfuly',
            'offer' => $offer_b,
        ]);
    }


    public static function update_brand_offers($account_id, $offer_id, $brands, $ActiveInactive)
    {
        // change offers
        $offers = account::find($account_id)->offers()->where(['offer_id'=> $offer_id, 'account_id'=> $account_id])->get();
        $offers_actived = [];
        $offers_inactived = [];

        // toActive
        if($ActiveInactive == 'toActive' or $ActiveInactive == 'activeInactive'){
            foreach($brands as $brand){
                if($offers->contains('brand_id', $brand)){
                    $offer = $offers->firstWhere('brand_id', $brand);
                    if($offer->statut == 0){
                        offer::find($offer->id)->update(['statut' => 1]);
                        array_push($offers_actived, $offer->brand_id);
                    }
                }else{
                    $offer = offer::create(['offer_id'=> $offer_id, 'brand_id' => $brand, 'statut' => 1, 'account_id' => $account_id]);
                    array_push($offers_actived, $offer->brand_id);
                }
            }            
        }

        // toInactive
        if($ActiveInactive == 'toInactive' or $ActiveInactive == 'activeInactive'){
            foreach($brands as $brand){
                if($offers->contains('brand_id', $brand)){
                    $offer = $offers->firstWhere('brand_id', $brand);
                    if($offer->statut == 1){
                        offer::find($offer->id)->update(['statut' => 0]);
                        array_push($offers_inactived, $offer->brand_id);
                    }
                }
            }        
        }

        return $ActiveInactive == 'toActive' ? $offers_actived : $offers_inactived ;

    }
    public static function update_offer_product($account_id, $offer_id, $account_products, $ActiveInactive)
    {
        // change offers
        $product_offers = account::with(['account_product' => function($query) use($offer_id){
            $query->with(['product_offer' => function($query) use($offer_id){
                $query->where('product_offer.offer_id', $offer_id);
            }]);
        }])->find($account_id)->account_product
            ->map(function($account_product) {
                if(count($account_product->product_offer) > 0){
                    return $account_product->product_offer->first();
                }
        })->filter()->values();

        $products_actived = [];
        $products_inactived = [];

        // toActive
        if($ActiveInactive == 'toActive' or $ActiveInactive == 'activeInactive'){
            foreach($account_products as $account_product){
                if($product_offers->contains('account_product_id', $account_product)){
                    $product_offer = $product_offers->firstWhere('account_product_id', $account_product);
                    if($product_offer->status == 0){
                        product_offer::where('id',$product_offer->id)->update(['status' => 1]);
                        array_push($products_actived, $account_product);
                    }
                }else{
                    $product_offer = product_offer::create(['offer_id'=> $offer_id, 'account_product_id' => $account_product, 'status' => 1]);
                    array_push($products_actived, $account_product);
                }
            }
        }

        // toInactive
        if($ActiveInactive == 'toInactive' or $ActiveInactive == 'activeInactive'){
            foreach($account_products as $account_product_id1){
                if($product_offers->contains('account_product_id', $account_product_id1)){
                    $product_offer = $product_offers->firstWhere('account_product_id', $account_product_id1);
                    if($product_offer->status == 1){
                        // dd(product_offer::find($product_offer->id));
                        product_offer::where('id',$product_offer->id)->update(['status' => 0]);
                        array_push($products_inactived, $product_offer->account_product_id);
                    }
                }
            }
        }
        return $ActiveInactive == 'toActive' ? $products_actived : $products_inactived ;
    }
    public static function productsByOffer($id,array $filters, bool $active, $paginate=false){

        $filters = self::filterColumns($filters, ['title', 'reference']);
        $account = User::find(Auth::user()->id)->accounts->first();
        $products = account::with([
            'offers' => function($query) use($id, $filters, $active){
                $query->where(['offers.id' => $id])
                    ->with(['product_offer' => function($query) use($filters, $active){
                        $query->where('status', $active)
                            ->with(['account_product' => function($query) use($filters){
                                $query->with(['products' => function($query) use($filters){
                                    $query->with('images');                                        
                                    // filtering by search
                                    if($filters['search'] == null){
                                        //filtering by columns
                                        if($filters['filters']['title'] != null){
                                            $query->where('title', 'like', "%{$filters['filters']['title']}%" );
                                        }
                                        if($filters['filters']['reference'] != null){
                                            $query->where('reference', 'like', "%{$filters['filters']['reference']}%" );
                                        }
                                    }else{
                                        $query->where('title', 'like', "%{$filters['search']}%" )
                                            ->orWhere('reference', 'like', "%{$filters['search']}%" );
                                    }
                                    // filtering date
                                    if($filters['startDate'] != null and $filters['startDate'] != null){
                                        $query->whereBetween('created_at', [$filters['startDate'], $filters['endDate']]);
                                    }                                    
                                }]);
                                }]);
                    }]);
            }])->find($account->id)->offers->first()->product_offer
                ->map(function($product_offer){
                    if(count($product_offer->account_product->products) > 0){
                        $product = collect($product_offer->account_product->products->first())
                            ->put('image', collect($product_offer->account_product->products->first()->images->first())->only('photo', 'photo_dir') )
                            ->only('title', 'reference', 'created_at', 'image')
                            ->put('id', $product_offer->account_product->id);
                        return $product;
                    }                     
            })->filter();
            if($paginate==false){
                return $products->toArray();
            }
        //filtering by page and per_page
        $total_rows = $products->count();
        $filters['pagination']['per_page'] = ($filters['pagination']['per_page'] == null or $filters['pagination']['per_page']== 0) ? 10 : $filters['pagination']['per_page'];
        $pages = ceil($total_rows/$filters['pagination']['per_page']) != 0 ? range(0, ceil($total_rows/$filters['pagination']['per_page'])-1) : [0];
        $filters['pagination']['current_page'] = ($filters['pagination']['current_page'] == null or $filters['pagination']['current_page']> end($pages))  ? 1 : $filters['pagination']['current_page'] +1;
        $products = $products->forpage($filters['pagination']['current_page'], $filters['pagination']['per_page'])->values()->toArray();
        return [
            'data' => $products,
            'pages' => $pages,
            'per_page' => $filters['pagination']['per_page'],
            'current_page' => $filters['pagination']['current_page'] >= 1 ? $filters['pagination']['current_page']-1 : 0,
            'total' => $total_rows
        ];
    }

    public static function brandsByOffer($id, $filters, bool $active, $paginate=false){
        // here we define the filters columns if non exist we give them the value null
        //to avoid them in the feltering by make this condition (!= null)
        $filters = self::filterColumns($filters, ['title', 'website', 'email']);
        $account = User::find(Auth::user()->id)->accounts->first();
        $brands = account::with([
            'offers' => function($query) use($id, $filters, $active){
                $query->where(['offers.offer_id' => $id])
                    ->where('offers.statut', $active)
                    ->with(['brands' => function($query) use($filters, $active){
                        $query->with('images');                                        
                            // filtering by search
                            if($filters['search'] == null){
                                //filtering by columns
                                if($filters['filters']['title'] != null){
                                    $query->where('title', 'like', "%{$filters['filters']['title']}%");
                                }
                                if($filters['filters']['website'] != null){
                                    $query->where('website', 'like', "%{$filters['filters']['website']}%");
                                }
                                if($filters['filters']['email'] != null){
                                    $query->where('email', 'like', "%{$filters['filters']['email']}%");
                                }
                            }else{
                                $query->where('title', 'like', "%{$filters['search']}%" )
                                    ->orWhere('website', 'like', "%{$filters['search']}%" )
                                    ->orWhere('email', 'like', "%{$filters['search']}%" );
                            }
                            // filtering date
                            if($filters['startDate'] != null and $filters['startDate'] != null){
                                $query->whereBetween('created_at', [$filters['startDate'], $filters['endDate']]);
                            }                                    
                    }]);
            }])->find($account->id)->offers
                ->map(function($offer){
                    if(count($offer->brands) != 0){
                        $brand = collect($offer->brands->first())
                            ->put('image', collect($offer->brands->first()->images->first())->only('photo', 'photo_dir'))
                            ->only('id', 'title', 'website', 'email', 'created_at', 'image');
                        return $brand;
                    }
            })->filter();
        //filtering by page and per_page
        if($paginate==false){
            return $brands->toArray();
        }
        $total_rows = $brands->count();
        $filters['pagination']['per_page'] = ($filters['pagination']['per_page'] == null or $filters['pagination']['per_page']== 0) ? 10 : $filters['pagination']['per_page'];
        $pages = ceil($total_rows/$filters['pagination']['per_page']) != 0 ? range(0, ceil($total_rows/$filters['pagination']['per_page'])-1) : [0];
        $filters['pagination']['current_page'] = ($filters['pagination']['current_page'] == null or $filters['pagination']['current_page']> end($pages))  ? 1 : $filters['pagination']['current_page'] +1;
        $brands = $brands->forpage($filters['pagination']['current_page'], $filters['pagination']['per_page'])->values()->toArray();
        return [
            'data' => $brands,
            'pages' => $pages,
            'per_page' => $filters['pagination']['per_page'],
            'current_page' => $filters['pagination']['current_page'] >= 1 ? $filters['pagination']['current_page']-1 : 0,
            'total' => $total_rows
        ];
    }

    public static function filterColumns($filters, $columns){
        $filters['search'] = isset($filters['search']) ? $filters['search'] : null;
        foreach($columns as $column){
            $filters['filters'][$column] = isset($filters['filters'][$column]) ? $filters['filters'][$column] : null;
        }
        $filters['startDate'] = isset($filters['startDate']) ? $filters['startDate'] : null;
        $filters['endDate'] = isset($filters['endDate']) ? $filters['endDate'] : null;
        $filters['pagination']['current_page'] = isset($filters['pagination']['current_page']) ? $filters['pagination']['current_page'] : null;
        $filters['pagination']['per_page'] = isset($filters['pagination']['per_page']) ? $filters['pagination']['per_page'] : null;
        return $filters;
    }

    public static function getPagination($data, $per_page, $current_page){
        $total_rows = $data->count();
        $per_page = ($per_page == null or $per_page== 0) ? 10 : $per_page;
        $pages = ceil($total_rows/$per_page) != 0 ? range(0, ceil($total_rows/$per_page)-1) : [0];
        $current_page = ($current_page == null or $current_page> end($pages))  ? 1 : $current_page +1;
        $data = $data->forpage($current_page, $per_page)->values()->toArray();
        return [
            'statut'=>1,
            'data' => $data,
            'per_page' => $per_page,
            'current_page' => $current_page,
            'total'=>$total_rows
        ];
    }

}

