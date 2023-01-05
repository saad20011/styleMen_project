<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\customer;
use Illuminate\Support\Facades\Auth;
use App\Models\account;
use App\Models\user;
use App\Models\order;
use App\Models\offer;
use App\Models\address;
use App\Models\account_code;
use Validator;
use Carbon\Carbon;


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
            'customer.id' => 'required',
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
        //récuperer le code de la commande
        $account = User::find(Auth::user()->id)->accounts->first();
        $code=account_code::where(['controleur'=>'Orders','account_id'=>$account->id])->first();
        //ajouter le client
        $request_customer=new Request();
        $request_customer->merge($request->input('customer'));
        $customer = CustomerController::store( $request_customer, $local=1);
        //remplir la table order
        $account_user = User::find(Auth::user()->id)->account_user->first();
        $request->merge([
            'customer_id'=>$customer->id,
            'account_city_id'=>$request->customer['adresse']['account_city_id'],
            'code'=>$code->prefixe.$code->compteur+1,
            'status_id'=>1,
            'account_user_id'=>$account_user->id
        ]);
        //creer la commande
        $order = order::create($request->all());
        //mettre a jour le code
        $update_code=account_code::find($code->id)->update(['compteur'=>$code->compteur+1]);
        //ajouter le commentaire
        $order->subcomment_order_comment()->attach(1, 
        [
            'status_id' => $order->status_id,
            'account_user_id' => $order->account_user_id,
            'title' => "Commande en attente",
            'postpone' => Carbon::now()->addDay()->format('Y-m-d'),
        ]);
        //recuperer le prix d'envoi
        $carrier_price=null;
        //remplir la table order_products
        foreach ($request->order_products as $key => $orderp) {
             $offer=offer::find($orderp['offer_id']);
             $request_orderp = new Request();
             $request_orderp->merge($orderp);
             $request_orderp->merge([
                'account_user_id'=>$account_user->id,
                'order_id'=>$order->id,
                'price'=>$offer->price,
                'statut'=>1
            ]);
             if ($carrier_price){
                if ($offer->shipping_price<$carrier_price){
                    $carrier_price=$offer->shipping_price;
                }
            }else{
                $carrier_price=$offer->shipping_price;
            }
            $order_product = OrderproductsController::store( $request_orderp, $local=1);
        }
        //mettre a jour les frais d'envoi
        $update_order=order::find($order->id)->update(['carrier_price'=>$carrier_price]);

        return $order;
        
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
