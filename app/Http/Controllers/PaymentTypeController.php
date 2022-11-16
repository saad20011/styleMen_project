<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\payment_type;
use App\Models\User;

class PaymentTypeController extends Controller
{

    
    public function index(Request $request)
    {
        $payment_types = payment_type::get();

        return response()->json([
            'statut' => 1,
            'payment_types' => $payment_types,
        ]);
    }

    public function create(Request $request)
    {

    }

    public function store(Request $request)
    {
        request()->validate([
            'code' => 'required',
            'name' => 'required',
        ]);
    
        $payment_type = payment_type::create($request->all());
    
        return response()->json([
            'statut' => 1,
            'payment_type' => $payment_type,
        ]);
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $payment_type = payment_type::find($id);
        return response()->json([
            'statut' => 1,
            'payment_type' => $payment_type,
        ]);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'code' => 'required',
            'name' => 'required'
        ]);
        $payment_type = payment_type::find($id);
        $payment_type->code = $request->input('code');
        $payment_type->name = $request->input('name');
        $payment_type->save();
        return response()->json([
            'statut' => 1,
            'payment_type' => $payment_type,
        ]);
    }


    public function destroy($id)
    {
        $payment_type_b =  payment_type::find($id);
        $payment_type = payment_type::find($id)->delete();
        return response()->json([
            'statut' => 'deleted successfuly',
            'payment_type' => $payment_type_b,
        ]);
    }
}
