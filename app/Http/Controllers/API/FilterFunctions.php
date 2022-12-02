<?php
namespace App\Http\Controllers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class FilterFunctions extends Controller
{
    public function updateModel($first_model, $id_model, $second_model, $array){
        $brand = $first_model::find($id_model);
        $imageables = $first_model::find($id_model)->imageables;
        foreach($imageables as $imageable ){
            if(in_array($imageable->image_id, $array )==true ){
                if( $imageable->statut==0)
                $second_model::find($imageable->id)->update(['statut'=>1]);
            
            }else{
                $second_model::find($imageable->id)->update(['statut'=>0]);
            }
        }
        foreach($array as $image ){
            $exist = collect($imageables)->contains(function ($imageable) use($image, $id_model, $brand) {
                return $imageable->image_id == $image and $imageable->imageable_id == $id_model;
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
    }
}