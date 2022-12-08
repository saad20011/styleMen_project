<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\charge;
use App\Models\charge_type;
use App\Models\payment_commission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ChargeController extends Controller
{

    public function index(Request $request)
    {
        $charges = charge::get();

        return response()->json([
            'status' => 1,
            'charges' => $charges,
        ]);
    }

    public function create(Request $request)
    {
        $account_user = account_user::where('user_id',Auth::user()->id)
            ->first(['account_id','user_id']);
            
        $charge_types = charge_type::where('account_id',$account_user->account_id)
            ->get();
        $payment_commission = payment_commission::get();
        return response()->json([
            'statut' => 1,
            'charge_types' => $charge_types,
            'payment_commission' => $payment_commission,
        ]);
    }

    public function store(Request $request)
    {
        request()->validate([
            'account_id' => 'required',
            'charge_type_id' => 'required',
            'montant' => 'required',
            'payment_commission_id' => '',
            'comment' => 'required',
            'date' => 'required',
            'statut' => 'required',
        ]);
    
        $charge = charge::create($request->all());
    
        return response()->json([
            'statut' => 'product created successfuly',
            'product' => $charge,
        ]);
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $charge = charge::find($id);
        $charge_types = charge_type::get();
        $payment_commission = payment_commission::get();
        return response()->json([
            'statut' => 1,
            'charge' => $charge,
            'charge_types' => $charge_types,
            'payment_commission' => $payment_commission,
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'account_id' => 'required',
            'charge_type_id' => 'required',
            'montant' => 'required',
            'payment_commission_id' => '',
            'comment' => 'required',
            'date' => 'required',
            'statut' => 'required',
        ]);
        $charge = charge::find($id);
        $charge->account_id = $request->input('account_id');
        $charge->charge_type_id = $request->input('charge_type_id');
        $charge->montant = $request->input('montant');
        $charge->payment_commission_id = $request->input('payment_commission_id');
        $charge->comment = $request->input('comment');
        $charge->date = $request->input('date');
        $charge->statut = $request->input('statut');
        $charge->save();
        return response()->json([
            'statut' => 'your phone type is updated successfuly',
            'charge' => $charge,
        ]);
    }


    public function destroy($id)
    {
        $charge = charge::where('id',$id)->delete();
        return response()->json([
            'statut' => 'deleted successfuly',
            'role' => $charge,
        ]);
    }
}
