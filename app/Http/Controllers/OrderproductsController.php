<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\customer;
use Illuminate\Support\Facades\Auth;
use App\Models\account;
use App\Models\user;
use App\Models\order_product;
use App\Models\address;
use App\Models\account_code;
use Validator;

class OrderproductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public static function store(Request $request,$local=0)
    {
        $validator = Validator::make($request->all(), [
            'product_variationattribute_id' => 'required',
            'offer_id' => 'required',
            'quantity' => 'required',
            'account_user_id' => 'required',
            'order_id' => 'required',
            'price' => 'required',
            'statut' => 'required',
        ]);
        if ($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);     
        }
        $order_product = order_product::create($request->all());
        return $order_product;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
