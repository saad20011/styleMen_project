<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\image;
use App\Models\User;
use App\Models\account;
use App\Models\product;
use App\Models\imageable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelperFunctions;

class ImageController extends Controller
{

    public static function index(Request $request, $local=0, $paginate=true)
    {
        $filters = HelperFunctions::filterColumns($request->toArray(), ['type', 'price', 'imageable_id', 'brands', 'products']);
        $account = User::find(Auth::user()->id)->accounts->first();
        $all_images = account::find($account->id)->has_images;
        $images = account::with(['has_images' => function($query) use($filters){
            if($filters['filters']['type']  != null){
                $query->where('title', 'like', $filters['filters']['type'] );
            }
        }])->find($account->id)->has_images->map(function($image){
            return $image->only('id', 'photo', 'photo_dir');

        });
        

        $types = $all_images->unique('title')->values()->map(function($type){
            return $type->only('title');
        });
        $dataPagination = HelperFunctions::getPagination($images, $filters['pagination']['per_page'], $filters['pagination']['current_page']);
        if($local == 1){
            if($paginate == false){
                return $images->toArray();
            }
            return $dataPagination;
        };
        return response()->json([
            'statut' => 1,
            'types' => $types,
            'data' => $dataPagination
        ]);
    }

    public function create(Request $request)
    {
        //
    }

    public static function store(Request $request, $local=0,  $table=null,  $principal_image = 0 )
    {
        // dd($request->all());
            $validator = Validator::make($request->all(), [
                'type' => 'required',
                'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:204800',
                'related' => 'boolean'
            ]);
            if($validator->fails()){
                return response()->json([
                    'Validation Error', $validator->errors()
                ]);       
            };
        $imageType = $request->type;
        $account = User::find(Auth::user()->id)->accounts->first();
        $images = collect($request->images)->map(function($image) use($account, $imageType,$local,$table, $principal_image, $request ){
            $path = Storage::disk('public')->putFile('images/'.$imageType, $image); //store the image
            $image = $account->has_images()->create([
                'account_id' => $account->id,
                'title'=> $imageType,
                'photo'=> $path,
                'photo_dir'=>'/storage',
            ]);     
            if($table != null){
                $table->images()->attach($image->id , ['statut'=> $principal_image == true ? 1 : 2 ]);
            }
            return $image;
        });
        if($local == 1){
                return $images;
        };
        return response()->json([
            'statut' => 1,
            'data' => $images,
        ]);
    }

    public function show($id)
    {
        //
    }

    public function edit(Request $request, $id)
    {
        //
    }


    public static function update(Request $request, $local=0,  $table, array $images, $principal_image=0)
    {
        if(!$local){
            $validator = Validator::make($request->all(), [
                // 'images' => 'required|image|mimes:jpeg,webp,png,jpg,gif,svg|max:20480',
            ]);

            if($validator->fails()){
                return response()->json([
                    'Validation Error', $validator->errors()
                ]);       
            };
        }
        $imageables = $principal_image ==1
                ? $table->imageables->whereIn('statut',[0, 1]) 
                : $table->imageables->whereIn('statut', [0, 2]);
        //the table must have  realtion name images
        foreach($imageables as $imageable ){
            if(in_array($imageable->image_id, $images ) == true ){
                if($principal_image == 1){
                    if($imageable->statut != 1)
                        imageable::find($imageable->id)->update(['statut'=>1]);
                }else{
                    if($imageable->statut != 2)
                        imageable::find($imageable->id)->update(['statut'=>2]);
                }
            }else{
                if($imageable->statut != 0){
                    imageable::find($imageable->id)->update(['statut'=>0]);
                }
            }
        }
        $imageables = product::find(170)->imageables; 
        // dd($table->imageables->toArray());
        foreach($images as $image){
            $exist = collect($imageables)->contains('image_id',$image);
            if($exist == false ){
                if($principal_image == 0){
                    // dd($imageables->toArray());
                    // die();                    
                }

                $table->images()->attach($image, ['statut' => $principal_image == 0 ? 2 : 1]);
            }
        }
        if($local == 1)
            return true;
        return response()->json([
            'statut' => 1,
            'data' => true,
        ]);
    }


    public function destroy(Request $request, $id)
    {
        $request = collect($request->query());
        $imagesDeleted = collect($request['images'])->map(function($id){
            $image = image::with('imageables')->find($id);
            $delted = true;// Storage::disk('public')->delete($image->photo);
            if($delted){
                $imageablesDeleted = $image->imageables()->delete();
                $imagedeleted = image::find($id)->delete();
                return $image;
            }
        })->filter()->values();

        return response()->json([
            'statut' => 1,
            'data' => $imagesDeleted,
        ]);
    }
}


