<?php

namespace App\Http\Controllers;

use App\Models\variationattribute;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\account_user;
use App\Models\types_attribute;
use App\Models\account;
use App\Models\attribute;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VariationAttributesController extends Controller
{

    public static function index(Request $request, $local=0,$paginate=false, $productVar =null)
    {
        $filters = HelperFunctions::filterColumns($request->toArray(), ['reference', 'title', 'shipping_price', 'attributes', 'products', 'offers']);
        $account_user = User::find(Auth::user()->id)->account_user->first();
        $accounts_users = account::find($account_user->account_id)->account_user->pluck('id')->toArray();
        $variationAttributes = variationattribute::where(['attribute_id' => null , 'variationattribute_id' => null])
            ->whereIn('account_user_id' , $accounts_users)
            ->with(['attributes'=>function($query){
                $query->with('type_attribute');
            },'products'])->get()
            ->map(function($variation) use($filters, $productVar){
                $variation->products = $products = $variation->products->map(function($product){
                    return $product->only('id', 'title');
                });
                if($productVar !=null){
                    $variation->product_variationAttributes = $product_variationAttributes = $variation->product_variationAttributes->map(function($product_variationAttribute) use($productVar){
                        if($product_variationAttribute->statut == 1 and $product_variationAttribute->product_id == $productVar){
                            return $product_variationAttribute->only('id', 'product_id', 'statut');
                        };
                    })->filter();                    
                }

                $variation->attributes = $attributes = $variation->attributes->map(function($attr){
                    return ['id'=>$attr->id, 'title'=>$attr->title,
                                'typeAttributeId'=>$attr->type_attribute->id,
                                'typeAttributeTitle'=>$attr->type_attribute->title];
                });
                $attributesExisting = HelperFunctions::filterExisting($filters['filters']['attributes'], $attributes->pluck('id'));
                $productsExisting = HelperFunctions::filterExisting($filters['filters']['products'], $products->pluck('id'));
                if($productsExisting and $attributesExisting ){
                    if($productVar != null){
                        if(count($product_variationAttributes)>0){
                            return $variation->attributes;
                        }
                    }else{
                        return $variation->attributes;
                    }
                    
                }
            })->collapse()->unique('id')->values()->sortBy('typeAttributeId');;
        $dataPagination = HelperFunctions::getPagination($variationAttributes, $filters['pagination']['per_page'], $filters['pagination']['current_page']);

        if($local == 1){
            if($paginate == true){
                return $dataPagination;
            }else{
                return $variationAttributes->toArray();
            }
        };
        return response()->json([
            'statut' => 1,
            'data' => $variationAttributes
        ]);

    }


    public function create()
    {
        //
    }


    public static function store(Request $request, $local=0, $validation=1)
    {
        $account_user = User::find(Auth::user()->id)->account_user->first();
        $accounts_users = account::find($account_user->account_id)->account_user->pluck('id')->toArray();
        if($validation == 1){
            $validator = Validator::make($request->all(), [
                'variations.*' => ['required',
                    function ($attribute, $value, $fail)  use($accounts_users) {
                        $type_att = types_attribute::whereIn('account_user_id', $accounts_users)->find($value['attribute_type']);
                        if($type_att == null){
                            $fail('The attribute type '.$value['attribute_type'].' is invalid.');
                        
                        }else{
                            $attributes = attribute::where(['types_attribute_id' => $type_att->id, 'account_user_id' => $accounts_users])
                                ->get()->pluck('id')->toArray();
                            foreach($value['elements'] as $element){
                                if(!in_array($element,$attributes)){
                                    $fail('The attribute '.$element.' is invalid.');
                                }
                            }
                        }
                    },
                ],
            ]);

            if($validator->fails()){
                if($local == 1){
                    return [
                        'statut' => 0,
                        'data' => $validator->errors()
                    ];
                }
                return response()->json([
                    'Validation Error', $validator->errors()
                ]);       
            }
        };
        $attributes = collect($request->variations)->pluck('elements')->toArray();

        $cases = HelperFunctions::generateCases($attributes);
        $variationAttributes = variationattribute::with('variationAttributes')
            ->where(['attribute_id' => null , 'variationattribute_id' => null])
            ->get('id')
            ->map(function($element){
                $element->variationAttributes =$element->variationAttributes->pluck('attribute_id')->toArray();
                return $element->only('id', 'variationAttributes');
            })->toArray();

        $variations = array();
        foreach($cases as $case){
            $exists = false ;
            if(count($variationAttributes) > 0){
                foreach($variationAttributes as $variationAttribute){
                    $arr = $variationAttribute['variationAttributes'];
                    $same = empty(array_diff($case, $arr)) && empty(array_diff($arr, $case));
                    if ($same) {
                        // la variation déja existe 
                        array_push($variations, $variationAttribute['id']);
                        $exists = true;
                        break;
                    }
                }   
            }
            if(!$exists){
                // la variation n'existe existe pas
                $new_variation = variationattribute::create([
                    'account_user_id' => 1,
                ]);
                foreach($case as $attribute){
                    variationattribute::create([
                        'account_user_id' => 1,
                        'variationAttribute_id' => $new_variation->id,
                        'attribute_id' => $attribute
                    ]);
                }
                array_push($variations, $new_variation->id) ;
            } 
        }
        if($local == 1){
            return[
                'statut' => 1,
                'data' => $variations
            ];
        }
        return response()->json([
            'statut' => 1, 
            'data' => $variations,
        ]);
    }


    public function show(VariationAttributesController $variationAttributesController)
    {
        //
    }


    public function edit(VariationAttributesController $variationAttributesController)
    {
        //
    }

    public function update(Request $request, VariationAttributesController $variationAttributesController)
    {
        //
    }


    public function destroy(VariationAttributesController $variationAttributesController)
    {
        //
    }
    
    function generateCases($attributes) {
        $cases = [[]];
        foreach ($attributes as $attributeValues) {
          $newCases = [];
          foreach ($cases as $case) {
            foreach ($attributeValues as $value) {
              $newCases[] = array_merge($case, [$value]);
            }
          }
          $cases = $newCases;
        }
        return $cases;
      }
      public static function variationFilters(Request $request, $local=0,$filters = 0)
      {
          $account_user = User::find(Auth::user()->id)->account_user->first();
          $accounts_users = account::find($account_user->account_id)->account_user->pluck('id')->toArray();
          $variationAttributes = variationattribute::where(['attribute_id' => null , 'variationattribute_id' => null])
              ->whereIn('account_user_id' , $accounts_users)
              ->with('attributes')->get()
              ->map(function($variation){
                  $attributes = $variation->attributes->map(function($attr){
                      return $attr->title;
                  })->toArray();
                  return ['id'=>$variation->id, "title"=>implode("-", $attributes)];
              });
          if($local == 1) return $variationAttributes;
          return response()->json([
              'statut' => 1,
              'data' => $variationAttributes
          ]);
  
      }
}
