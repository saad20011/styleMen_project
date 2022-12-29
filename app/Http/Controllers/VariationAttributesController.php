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
use Illuminate\Validation\Rule;

class VariationAttributesController extends Controller
{

    public function index()
    {
        //
    }


    public function create()
    {
        //
    }


    public function store(Request $request, $local=0, $validation=1)
    {
        $account_user = User::find(Auth::user()->id)->account_user->first();
        $accounts_users = account::find($account_user->account_id)->account_user->pluck('id')->toArray();
        // dd($request->all());
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

        $cases = $this->generateCases($attributes);
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
    
}
