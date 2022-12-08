<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\image;
use App\Models\User;
use App\Models\account;
use App\Models\imageable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ImageController extends Controller
{

    public function index(Request $request)
    {
        $account = User::find(Auth::user()->id)->accounts->first();
        $images = account::find($account->id)->images;

        return response()->json([
            'statut' => 1,
            'data' => $images,
        ]);
    }

    public function create(Request $request)
    {
        //
    }

    public static function store(Request $request, $local=0, $table=null, $model_name, $principal_image = 0 )
    {
        if($local == 0){
            $validator = Validator::make($request->all(), [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

            ]);

            if($validator->fails()){
                return response()->json([
                    'Validation Error', $validator->errors()
                ]);       
            };
        }

        $account = User::find(Auth::user()->id)->accounts->first();
        $path = Storage::disk('public')->putFile('images/'.$model_name, $request->image); //store the image
            $image = $account->has_images()->create([
                'account_id' => $account->id,
                'title'=> 'brand',
                'photo'=> $path,
                'photo_dir'=>'/storage',
            ]);
        if($local ==1 )
            $table->images()->attach($image->id , ['statut'=> !$principal_image ? 1 : 2 ]);
            return $image;


        return response()->json([
            'statut' => 1,
            'image' => $image,
        ]);
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }


    public static function update(Request $request, $id=null, $local=0,  $table, array $images, $model_name, $principal_image=0)
    {
        if(!$local){
            $validator = Validator::make($request->all(), [
                'images' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            ]);

            if($validator->fails()){
                return response()->json([
                    'Validation Error', $validator->errors()
                ]);       
            };
        }
        $imageables = !$principal_image
                ? $table->imageables->whereNotIn('statut',[2]) 
                : $table->imageables->where('statut',2);
                
        foreach($imageables as $imageable ){
            if(in_array($imageable->image_id, $images ) == true ){
                if(!$principal_image){
                    if($imageable->statut==0)
                        imageable::find($imageable->id)->update(['statut'=>1]);
                }else{
                    if($imageable->statut != 2)
                        imageable::find($imageable->id)->update(['statut'=>2]);
                }
            }else{
                    imageable::find($imageable->id)->update(['statut'=>0]);
            }
        }
        $exist = collect($imageables)->contains(function ($imageable) use($request, $table) {
            return $imageable->image_id == $request->image and $imageable->imageable_id == $table->id;
        });
        if(!$exist){
            $table->imageables()->create([
                    'image_id'=> $request->image,
                    'imageable_id' => $table->id,
                    'imageable_type'=> "App\Models\\".$model_name,
                    'statut'=> !$principal_image ? 1 : 2,
                ]);
        }
        // $image_updated = $table->has_images();
        if($local == 1)
            return true;

        return response()->json([
            'statut' => 1,
            'data' => true,
        ]);
    }


    public function destroy($id)
    {
        $image_b =  image::find($id);
        $image = image::find($id)->delete();
        return response()->json([
            'statut' => 1,
            'image' => $image_b,
        ]);
    }
}


