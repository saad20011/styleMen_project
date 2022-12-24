<?php

namespace App\Http\Controllers;
use App\Http\Controllers\PhoneController;
use App\Http\Controllers\AddressController;
use Illuminate\Http\Request;
use App\Models\supplier;
use App\Models\address;
use App\Models\account;
use App\Models\phone_type;
use App\Models\phone;
use App\Models\city;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{

    public function index(Request $request)
    {
        $account = User::find(Auth::user()->id)->accounts->first();
        $suppliers = account::find($account->id)->suppliers;
        foreach($suppliers as $supplier){
            $supplier->addresses = supplier::find($supplier->id)->addresses;
            $supplier->phones = supplier::find($supplier->id)->phones;
        }
        return response()->json([
            'statut' => 1,
            'data' => $suppliers,
        ]);
    }

    public function create(Request $request)
    {
        //
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required',
            'statut' => 'required',
            'phone_type_id' => 'required',
            'phone' => 'required',
            'account_city_id' => 'required',
            'address' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'Validation Error'=> $validator->errors()
            ]);
        };
        $account = User::find(Auth::user()->id)->accounts->first();
        $account = account::find($account->id);
        $supplier = $account->suppliers()->create($request->all());
        $request_phone = new Request($request->only('phone', 'phone_type_id'));
        $phone = PhoneController::store( $request_phone, $local=1, $supplier);
        $request_address = new Request($request->only('address', 'account_city_id'));
        $phone = AddressController::store( $request_address, $local=1, $supplier);
        return response()->json([
            'statut' => 1,
            'data' => $phone 
        ]);
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $supplier = supplier::find($id);

        $cities = city::get();
        $phone_types = phone_type::get();

        return response()->json([
            'statut' => 1,
            'cities' => $cities,
            'phone_types' => $phone_types,
            'supplier' => $supplier
        ]);
    }

    public function update(Request $request, $id)
    {
        $account = User::find(Auth::user()->id)->accounts->first();
        $validator = Validator::make(collect($request->all())->put('id',$id)->all(), [
            'title' => '',
            'phone' => '',
            'phone_id' => 'required',
            'address' => 'required',
            'address_id' => 'required',
            'statut' => '',
            'id'=>'exists:suppliers,id,account_id,'.$account->id,
        ]);

        if($validator->fails()){
            return response()->json([
                'Validation Error', $validator->errors()
            ]);       
        }

        $account = account::find($account->id);
        $supplier = supplier::find($id)->update($request->all());
        $supplier_up = supplier::find($id);
        $supplier_up->phone = PhoneController::update(new Request($request->only('phone', 'phone_id')), $request->phone_id, $local=1);
        $supplier_up->address = AddressController::update(new Request($request->only('address', 'address_id')), $request->address_id, $local=1);


        return response()->json([
            'statut' => 1,
            'data' => $supplier_up

        ]);
    }

    public function destroy($id)
    {
        $supplier_b =  supplier::find($id);
        $supplier = supplier::find($id)->delete();
        return response()->json([
            'statut' => 'deleted successfuly',
            'supplier' => $supplier_b,
        ]);
    }
}
