<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\imageable;
use App\Models\account;
use App\Models\brand_source;
use App\Models\brand;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\BrandSourceController;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $account = User::find(Auth::user()->id);
        $brands = account::find($account->id)->brands()->paginate(20);
        foreach($brands as $brand){
            $brand->sources = brand::find($brand->id)
                ->sources;
            $brand->images = brand::find($brand->id)->images;
        }

        return response()->json([
            'statut' =>$account->id,
            'brands ' =>$brands,
        ]);
    }

    public function create(Request $request)
    {
        $account = User::find(Auth::user()->id);
        $sources = account::with('has_images','sources')->find($account->id);
        return response()->json([
            'statut' => 1,
            'sources' => $sources,
        ]);

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

        $account = User::find(Auth::user()->id);
        $new_brand = BrandSourceController::create( $account->id, $request->only('title','website','email','statut'));
        $brand_sources = BrandSourceController::update_brand_source( $new_brand->id, $account->id,$request->sources );
        $brand_image = BrandSourceController::create_brand_image( $new_brand->id, $account->id,$request->image );
        $brand = brand::with('images', 'sources')->find($new_brand->id);


        return response()->json([
            'statut' => 'brand created successfuly',
            'brand' => $brand,
        ]);
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $account = User::find(Auth::user()->id);
        $brand = brand::with('sources()','images')
            ->find($id);
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
            'images.*' => 'exists:images,id',
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };
        $account = User::find(Auth::user()->id);
        $change_sources = BrandSourceController::update_brand_source( $id, $account->id,$request->sources );
        $change_images = BrandSourceController::update_brand_imageable( $id,$request->images );

        $brands = brand::with('images', 'sources')->find($id);

        return response()->json([
            'statut' => 1,
            'brands' => $brands,
        ]);
    }


    public function destroy($id)
    {
        $brand_deleted = brand::where('id',$id)->get();
        $brand = brand::where('id',$id)->delete();
        return response()->json([
            'statut' => 1,
            'brand' => $brand_deleted,
        ]);
    }
}
