<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\brand;
use App\Models\brand_source;
use App\Models\account;
use App\Models\imageable;
use Illuminate\Support\Facades\Storage;


class BrandSourceController extends Controller
{

    public function index()
    {
        //
    }

    public static function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        //
    }

    public static function update_brand_source($brand_id, $account_id, $sources)
    {
    // change sources
    $brand = brand::find($brand_id);
    $brand_sources = brand::find($brand_id)->brand_sources;

    foreach($brand_sources as $brand_source ){
        if(in_array($brand_source->source_id, $sources )==true ){
            if( $brand_source->statut==0)
            brand_source::find($brand_source->id)->update(['statut'=>1]);
        
        }else{ 
            brand_source::find($brand_source->id)->update(['statut'=>0]);
        }
    }
    foreach($sources as $source ){
        $exist = collect($brand_sources)->contains('source_id',$source);
        if($exist == false ){
        $brand->sources()->attach($source, [
            'account_id'=> $account_id,
            'statut'=>1,
        ]);
        }
    }
    return true;
}

    public static function update_brand_imageable($brand_id, $images)
    {
        $brand = brand::find($brand_id);
        $imageables = brand::find($brand_id)->imageables;
        foreach($imageables as $imageable ){
            if(in_array($imageable->image_id, $images )==true ){
                if( $imageable->statut==0)
                imageable::find($imageable->id)->update(['statut'=>1]);
            
            }else{
                imageable::find($imageable->id)->update(['statut'=>0]);
            }
        }
        foreach($images as $image ){
            $exist = collect($imageables)->contains(function ($imageable) use($image, $brand_id) {
                return $imageable->image_id == $image and $imageable->imageable_id == $brand_id;
            });
            if($exist == false ){
            $brand->imageables()->create([
                    'image_id'=> $image,
                    'imageable_id' => $brand->id,
                    'imageable_type'=> 'App\Models\brand',
                    'statut'=>1,
                ]);
            }
        }
        return true;
    }
    public static function create_brand_image($brand_id, $account_id, $image)
    {
        $path = Storage::disk('public')->putFile('images/sources', $image); //store the image
        $brand = brand::find($brand_id);
        $brand->images()->create([
            'account_id' => $account_id,
            'title'=> 'brand',
            'photo'=> $path,
            'photo_dir'=>'/storage',
        ]);
        return true;
    }
    public function destroy($id)
    {
        //
    }
}
