<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\customer;
use Illuminate\Support\Facades\Auth;
use App\Models\account;
use App\Models\user;
use App\Models\address;
use Validator;

class CustomerController extends Controller
{

    public function index(Request $request)
    {
        $customers = customer::get();

        return response()->json([
            'you'=>$customers,
        ]);
    }

    public function create(Request $request)
    {

    }

    public function store(Request $request,$local=0)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'note' => 'required',
            'facebook' => 'required',
            'comment' => 'required',
            'statut' => 'required',
            'phones.*.title' => 'required',
            'phones.*.phone_type_id' => 'required',
            'adresse.address' => 'required',
            'adresse.account_city_id' => 'required',
        ]);
        
        if ($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        $customer = customer::create($request->all());
       
        foreach ($request['phones'] as $key => $phone) {
            $request_phone = new Request();
            $request_phone->merge(['phone'=>$request->input('phones.'.$key.'.title')]);
            $request_phone->merge(['phone_type_id'=>$request->input('phones.'.$key.'.phone_type_id')]);
            $phone = PhoneController::store( $request_phone, $local=1, $customer);
        }
        $request_address= new Request();
        $request_address->merge(['address'=>$request->input('adresse.address')]);
        $request_address->merge(['account_city_id'=>$request->input('adresse.account_city_id')]);
        $adresse = AddressController::store( $request_address, $local=1, $customer);

        return response()->json([
            'statut' => 'customer created successfuly',
            'product' => $customer,
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


    public function update(Request $request, $id , $local = 0)
    {
        //si la mise a jour est locale
        if($local==0){
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'note' => 'required',
                'facebook' => 'required',
                'comment' => 'required',
                'statut' => 'required',
                'phones.*.id' => 'required',
                'phones.*.title' => 'required',
                'phones.*.phone_type_id' => 'required',
                'adresse.id' => 'required',
                'adresse.address' => 'required',
                'adresse.account_city_id' => 'required',
            ]);
            if ($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }
        }
        //mise a jour du customer 
        $customer = customer::find($id);
        $updated=$customer->update(['name' =>  $request->input('name'),'note' =>  $request->input('note'),'facebook' =>  $request->input('facebook'),'comment' =>  $request->input('comment'),'statut'=> $request->input('statut')]);
        if($updated){
            //mise a jour phones
            foreach ($request['phones'] as $key => $phone) {
                $request_phone = new Request();
                $request_phone->merge(['title'=>$request->input('phones.'.$key.'.title')]);
                $request_phone->merge(['phone_type_id'=>$request->input('phones.'.$key.'.phone_type_id')]);
                PhoneController::update( $request_phone, $local=1, $request->input('phones.'.$key.'.id'));
            }
            //mise a jour addresses
            $request_address= new Request();
            $request_address->merge(['address'=>$request->input('adresse.address')]);
            $request_address->merge(['account_city_id'=>$request->input('adresse.account_city_id')]);
            AddressController::update( $request_address,$request->input('adresse.id'), $local=1);
            //recuperation du customer avec phones et addresse et account_city et city pour l'envoi de la reponse
            $customer = customer::with('phones','addresses.account_city.cities')->find($id);

            //l'envoi de la reponse
            if($local == 1)
                return $customer;
                
            return response()->json([
                'statut' => 1,
                'phone_type' => $customer,
            ]);
        }
        
        return response()->json([
            'statut' => 0,
            'data' => 'update customer error',
        ]);
        
    }


    public function destroy($id)
    {
        // avoir le client ne peux pas etre supprimer 7ite kankhadmo bih f bzaf d les tables sinon ghadi ykhassno nmass7o m3ah l'adresse les téléphones et les commandes li dawaz
    }
}
