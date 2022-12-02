<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\type_size;
use App\Models\User;
use App\Models\account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SizeTypeController extends Controller
{

    
    public function index(Request $request)
    {
        
        $account = User::find(Auth::user()->id)->accounts->first();

        $type_sizes = account::with('type_sizes')->find($account->id);

        return response()->json([
            'statut' => 1,
            'data' => $type_sizes,
        ]);
    }

    public function create(Request $request)
    {

    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        };
        $account = User::find(Auth::user()->id)->accounts->first();
        $type_size = account::find($account->id)->type_sizes()->create($request->all());
    
        return response()->json([
            'statut' => 1,
            'data' => $type_size,
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


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'description' => 'required',
        ]);

        $account = User::find(Auth::user()->id)->accounts->first();
        $type_size = type_size::find($id)->update($request->all());
        $type_size_updated = type_size::find($id);

        return response()->json([
            'statut' => 1,
            'type_size' => $type_size_updated,
        ]);
    }


    public function destroy($id)
    {
        $type_size_b =  type_size::find($id);
        $type_size = type_size::find($id)->delete();
        return response()->json([
            'statut' => 'deleted successfuly',
            'type_size' => $type_size_b,
        ]);
    }
}
