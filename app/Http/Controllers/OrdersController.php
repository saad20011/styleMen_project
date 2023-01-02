<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\customer;
use Illuminate\Support\Facades\Auth;
use App\Models\account;
use App\Models\user;
use App\Models\address;
use Validator;

class OrdersController extends Controller
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
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer.name' => 'required',
            'customer.phones.*.title' => 'required',
            'customer.phones.*.phone_type_id' => 'required',
            'customer.adresse.address' => 'required',
            'customer.adresse.account_city_id' => 'required',
            'brand_source_id' => 'required',
            'payment_method_id' => 'required',
            'payment_type_id' => 'required',
            'discount' => 'required',
            'comment' => 'required',
            'order_products.*.product_variationattribute_id' => 'required',
            'order_products.*.offer_id' => 'required',
            'order_products.*.quantity' => 'required',
        ]);
        if ($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);     
        }
        
        $request_customer=new Request();
        $request_customer->merge($request->input('customer'));
        // $request_customer=$request->customer;
        $customer = CustomerController::store( $request_customer, $local=1);
        
        return $customer;
        //hna ghadi nbda lkhedma 
        //ghadi ykhassni customer data
        //ghadi ykhassni orderdata
        //ghadi ykhassni cityaccount
        //
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
