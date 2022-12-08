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
        $this->validate($request,[
            'title' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'photo' => 'required',
            'photo_dir' => 'required',
            'statut' => 'required',
        ]);
        $supplier = supplier::find($id);
        $phone = phone::find($supplier['phone_id']);
        $address = address::find($supplier['address_id']);

        $supplier->title = $request->input('title');
        $phone->phone = $request->input('phone');
        $address->address = $request->input('address');
        $supplier->photo = $request->input('photo');
        $supplier->photo_dir = $request->input('photo_dir');
        $supplier->statut = $request->input('statut');
        $supplier->save();
        $phone->save();
        $address->save();

        return response()->json([
            'statut' => 1,
            'phone' => $phone,
            'address' => $address,
            'supplier' => $supplier,

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
