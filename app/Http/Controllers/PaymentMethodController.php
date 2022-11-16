<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\payment_method;
use App\Models\User;

class PaymentMethodController extends Controller
{

    
    public function index(Request $request)
    {
        $payment_methods = payment_method::get();

        return response()->json([
            'statut' => 1,
            'payment_methods' => $payment_methods,
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
    
        $payment_method = payment_method::create($request->all());
    
        return response()->json([
            'statut' => 1,
            'payment_method' => $payment_method,
        ]);
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $payment_method = payment_method::find($id);
        return response()->json([
            'statut' => 1,
            'payment_method' => $payment_method,
        ]);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'code' => 'required',
            'name' => 'required'
        ]);
        $payment_method = payment_method::find($id);
        $payment_method->code = $request->input('code');
        $payment_method->name = $request->input('name');
        $payment_method->save();
        return response()->json([
            'statut' => 1,
            'payment_method' => $payment_method,
        ]);
    }


    public function destroy($id)
    {
        $payment_method_b =  payment_method::find($id);
        $payment_method = payment_method::find($id)->delete();
        return response()->json([
            'statut' => 'deleted successfuly',
            'payment_method' => $payment_method_b,
        ]);
    }
}
