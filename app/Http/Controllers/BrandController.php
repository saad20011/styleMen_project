<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\account;
use App\Models\brand;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\BrandSourceController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\OfferController;

class BrandController extends Controller
{
    public static function index(Request $request, $local=0, $columns=false, $paginate=false)
    {
        $filters = OfferController::filterColumns($request->toArray(), ['title', 'website', 'email']);
        $account = User::find(Auth::user()->id)->accounts->first();

        $brands = account::with(['brands' => function($query) use($filters){
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
        }])->find($account->id)->brands->map(function($item)use($columns){
            $image = collect($item->images->first())->only('photo', 'photo_dir');
            return collect($item->only($columns == false?['id', 'title', 'website', 'email', 'created_at']:$columns))->put('image',$image);
        });
        $dataPagination = OfferController::getPagination($brands, intval($filters['pagination']['per_page']), intval($filters['pagination']['current_page']));
        if($local == 1){
            if($paginate == true){
                return $dataPagination;
            }else{
                return $brands->toArray();
            }
        };

        return response()->json(
            $dataPagination
        );
    }

    public function create(Request $request)
    {
        $account = User::find(Auth::user()->id)->accounts->first();
        $data = account::with('has_images','sources')->find($account->id);
        return response()->json([
            'statut' => 1,
            'data' => $data,
        ]);
    }

    public static function store_brand($account_id, $columns)
    {
        $brand = account::find($account_id)->brands()->create($columns);
        return $brand;
    }

    public function update_brand($brand_id, $new_data){
        $brand = brand::find($brand_id)->update($new_data);
        $brand_updated = brand::find($brand_id);
        return $brand_updated;
        
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'website' => 'required',
            'email' => 'required',
            'statut' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'sources.*' => 'required|exists:sources,id'
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };

        $account = User::find(Auth::user()->id)->accounts->first();
        $new_brand = $this->store_brand($account->id, $request->only('title','website','email','statut'));
        $brand_sources = BrandSourceController::update_brand_sources( $new_brand->id, $account->id,$request->sources );
        $brand_image = ImageController::store( new Request($request->only('image')), $local=1 ,$new_brand, 'Brand' );
        $brand = brand::with('images', 'sources')->find($new_brand->id);

        return response()->json([
            'statut' => 1,
            'data' => $brand,
        ]);
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $account = User::find(Auth::user()->id)->accounts->first();
        $brand = brand::with('sources','images')
            ->where(['id'=>$id, 'account_id'=> $account->id])
        ->first();
        $sources = account::find($account->id)->sources;

        return response()->json([
            'statut' => 1,
            'brand' => $brand,
            'sources' =>$sources,
        ]);
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'website' => 'required',
            'email' => 'required',
            'statut' => 'required',
            'sources.*' => 'exists:sources,id',
            'image.*' => 'exists:images,id',
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };
        $account = User::find(Auth::user()->id)->accounts->first();
        $brand_updated = $this->update_brand($id,$request->only('title', 'website', 'email', 'statut'));

        $brand_updated->image =  ImageController::update(new Request(['image'=>$request->image[0]]),'', $local=1,$brand_updated,$request->image,'Brand', $principal_image=1);
        $update_sources = BrandSourceController::update_brand_sources( $id, $account->id,$request->sources );

        $brands = brand::with('images', 'sources')->find($id);

        return response()->json([
            'statut' => 1,
            'brands' => $brands,
        ]);
    }


    public function destroy($id)
    {
        // $brand_deleted = brand::where('id',$id)->get();
        // $brand = brand::where('id',$id)->delete();
        // return response()->json([
        //     'statut' => 1,
        //     'brand' => $brand_deleted,
        // ]);
    }
}
