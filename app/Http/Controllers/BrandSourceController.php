<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\brand;
use App\Models\brand_source;
use App\Models\imageable;
use Illuminate\Support\Facades\Storage;


class BrandSourceController extends Controller
{


    public static function update_brand_sources($brand_id, $account_id, $sources)
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



}
