<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\product;
use App\Models\account_user;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        $products = product::get();

        return response()->json([
            'products '=>$products,
        ]);
    }

    public function create(Request $request)
    {
        $account_users = account_user::get();

        return response()->json([
            'statut' => 1,
            'account_users ' => $account_users,
        ]);

    }

    public function store(Request $request)
    {
        request()->validate([
            'reference' => 'required|unique:products',
            'title' => 'required|unique:products',
            'link' => 'required|unique:products',
            'price' => 'required',
            'sellingprice' => 'required',
            'account_user_id' => 'required',
            'photo' => 'required',
            'photo_dir' => 'required',
            'statut' => 'required',
        ]);
    
        $product = product::create($request->all());
    
        return response()->json([
            'statut' => 'product created successfuly',
            'product' => $product,
        ]);
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $product = product::find($id);

        return response()->json([
            'statut' => 0,
            'product ' => $product
        ]);

    }

    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'reference' => 'required',
            'title' => 'required',
            'link' => 'required',
            'price' => 'required',
            'sellingprice' => 'required',
            'account_user_id' => 'required',
            'photo' => 'required',
            'photo_dir' => 'required',
            'statut' => 'required',
        ]);
        $product = product::find($id);
        $product->title = $request->input('title');
        $product->statut = $request->input('statut');
        $product->save();
        return response()->json([
            'statut' => 'your phone type is updated successfuly',
            'product' => $product,
        ]);
    }


    public function destroy($id)
    {
        $product_b =  product::find($id);
        $product = product::find($id)->delete();
        return response()->json([
            'statut' => 'deleted successfuly',
            'product' => $product_b,
        ]);
    }
}
