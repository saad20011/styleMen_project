<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\brand;
use App\Models\source;
use App\Models\brands_sources;
use Auth;
use Validator;
use DB;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $brands = brand::get();
        $account_user = DB::table('account_users')
        ->join('users','users.id', '=', 'account_users.user_id')
        ->join('accounts','accounts.id', '=', 'account_users.account_id')
        ->where('users.id',Auth::user()->id)
        ->select('accounts.name as account_name',
                'accounts.id as account_id',
                'users.id as user_id'
        )->first();
        $brand_sources = DB::table('brands_sources')
            ->rightJoin('brands', function ($join) use($account_user){
                $join->on('brands_sources.brand_id', '=', 'brands.id')
                    ->where('brands_sources.account_id', '=', $account_user->account_id)
                    ->where('brands_sources.statut', '=', '1');
            })
            ->leftjoin('sources', 'sources.id', '=', 'brands_sources.source_id')
            ->select('sources.title as sources',
                    'brands_sources.statut as brand_source_statut',
                    'brands.title as brand_name',
                    'brands.id as brand_id',)
            ->orderBy('brands.id')->get()->toArray();
        // dd($brand_sources);
        $distinc = array();
        foreach($brand_sources as $key=>$value){
            if(array_key_exists($brand_sources[$key]->brand_id, $distinc)==false){
              $element = array(
                "brand_id"=>$brand_sources[$key]->brand_id,
                "brand_name"=>$brand_sources[$key]->brand_name,
                "sources"=>array($brand_sources[$key]->sources),
                );
              $distinc[$brand_sources[$key]->brand_id]= $element;
            }else{
                if(in_array($brand_sources[$key]->sources, $distinc[$brand_sources[$key]->brand_id]["sources"])==false ){
                    array_push($distinc[$brand_sources[$key]->brand_id]["sources"],$brand_sources[$key]->sources);
                }
            }
          }
        return response()->json([
            'statut' => 1,
            'brands ' => array_values($distinc),
        ]);
    }

    public function create(Request $request)
    {

    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'website' => 'required',
            'email' => 'required',
            'photo' => 'required',
            'photo_dir' => 'required',
            'statut' => 'required',
            'sources' => ''
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };
        $account_user = DB::table('account_users')
            ->join('users','users.id', '=', 'account_users.user_id')
            ->join('accounts','accounts.id', '=', 'account_users.account_id')
            ->where('users.id',Auth::user()->id)
            ->select('accounts.name as account_name',
                    'accounts.id as account_id',
                    'users.id as user_id'
            )->first();

        $brand_only = collect($request->only('title','website','email','photo','photo_dir','statut'))
            ->put('account_id',$account_user->account_id)->all();
        $brand = brand::create($brand_only);
        $brand_sources = collect($request->input('sources'))->map(function($source) use($account_user, $brand){
            $brand_source = brands_sources::create([
                'source_id'=> $source,
                'account_id' => $account_user->account_id,
                'brand_id' => $brand['id'],
            ]);
            return $brand_source;
        });
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
        $account_user = DB::table('account_users')
        ->join('users','users.id', '=', 'account_users.user_id')
        ->join('accounts','accounts.id', '=', 'account_users.account_id')
        ->where('users.id',Auth::user()->id)
        ->select('accounts.name as account_name',
                'accounts.id as account_id',
                'users.id as user_id'
        )->first();
        $sources = source::get(['id', 'title', 'photo', 'photo_dir', 'statut']);
        
        $brand_sources = DB::table('brands_sources')
            ->join('brands', function ($join) use($account_user, $id){
                $join->on('brands_sources.brand_id', '=', 'brands.id')
                    ->where('brands_sources.account_id', '=', $account_user->account_id)
                    ->where('brands_sources.brand_id', '=',  $id);
            })
            ->leftjoin('sources', 'sources.id', '=', 'brands_sources.source_id')
            ->select('sources.title as sources',
                    'brands_sources.statut as brand_source_statut',
                    'brands.title as brand_name',
                    'brands.id as brand_id',)
            ->orderBy('brands.id')->get()->toArray();
        $distinc = array();
        foreach($brand_sources as $key=>$value){
            if(array_key_exists($brand_sources[$key]->brand_id, $distinc)==false){
              $element = array(
                "brand_id"=>$brand_sources[$key]->brand_id,
                "brand_name"=>$brand_sources[$key]->brand_name,
                "sources"=>array($brand_sources[$key]->sources),
                );
              $distinc[$brand_sources[$key]->brand_id]= $element;
            }else{
                if(in_array($brand_sources[$key]->sources, $distinc[$brand_sources[$key]->brand_id]["sources"])==false ){
                    array_push($distinc[$brand_sources[$key]->brand_id]["sources"],$brand_sources[$key]->sources);
                }
            }
          }
        return response()->json([
            'statut' => 1,
            'brand' =>array_values($distinc),
            'sources' => $sources,
        ]);
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'website' => 'required',
            'email' => 'required',
            'photo' => 'required',
            'photo_dir' => 'required',
            'sources' => 'required',
            'brand_id' => 'required',
            'statut' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };
        $account_user = DB::table('account_users')
        ->join('users','users.id', '=', 'account_users.user_id')
        ->join('accounts','accounts.id', '=', 'account_users.account_id')
        ->where('users.id',Auth::user()->id)
        ->select('accounts.name as account_name',
                'accounts.id as account_id',
                'users.id as user_id'
        )->first();

        $brand_only = collect($request->only('title','website','email','photo','photo_dir','statut'));
        $brand = brand::find($id)->update($brand_only->all());
        $brand_updated = brand::find($id);

        // change sources brand
        $brand_sources = DB::table('brands_sources')
            ->join('brands', function ($join) use($id,$account_user){
                $join->on('brands_sources.brand_id', '=', 'brands.id')
                    ->where('brands_sources.account_id', '=', $account_user->account_id)
                    ->where('brands_sources.brand_id', '=', $id);
            })
            ->join('sources', 'sources.id', '=', 'brands_sources.source_id')
            ->select('sources.id as source_id',
                    'brands_sources.id as brands_source_id',
                    'brands_sources.statut as brand_source_statut',
            )->get();
        $request_sources = $request->input('sources');
        $reactive_or_add_source = collect($request_sources)->map(function($request_source) use($brand_sources,$account_user,$brand_updated){

            $in_array = collect($brand_sources->whereIn('source_id', [$request_source]))->first();
            if($in_array == null){
                //not exist
                $brand_source = brands_sources::create([
                    'source_id'=>$request_source,
                    'brand_id'=>$brand_updated['id'] ,
                    'account_id'=>$account_user->account_id ,
                ]);    
                return $brand_source;
            }else{
                // exist check
                if($in_array->brand_source_statut==0){
                    $brand = brands_sources::where('id',$in_array->brands_source_id)->update(['statut'=>1]);
                
                return $in_array;
            }}
            
        });
        $desactive_source = collect($brand_sources)->map(function($brand_source) use($request_sources){
            $in_array = in_array($brand_source->source_id, $request_sources);
            if($in_array == false){
                // exist
                $brand = brands_sources::where('id',$brand_source->brands_source_id)->update(['statut'=>0]);
        }});

        return response()->json([
            'statut' => 1,
            'brand' => $brand_updated,
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
