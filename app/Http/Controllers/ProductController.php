<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private function responseFormat($data, $message, $httpCode) {
        if($data->count() == 0) {
            return response()->json([
                'code' => 404,
                'message' => "Data not Found",
                'data' => null
            ], 404);
        }else{
            return response()->json([
                'code' => $httpCode,
                'message' => $message,
                'data' => $data
            ], $httpCode);
        }
    }

    public function index(Request $request)
    {
        $this->validate($request, [
            'search' => 'string',
            'take' => 'required|numeric',
            'skip' => 'required|numeric',
        ]);
        $query = $request->get('search');
        $take = $request->take ?? 2;
        $skip = $request->skip ?? 0;
        if ($query != null) {
            $products = Product::where('name', 'like', "%" . $query . "%")->skip($skip)->take($take);
        } else {
            $products = Product::skip($skip)->take($take);
        }
        return $this->responseFormat($products->get(), 'Success', 200);
    }

    public function show($id)
    {
        $products = Product::findOrFail($id);
        return $this->responseFormat($products, 'Success', 200);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'price' => 'required|decimal:10,2',
        ]);
        $product = Product::create($request->all());
        return $this->responseFormat($product, 'Success store', 201);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'price' => 'required|decimal:10,2',
        ]);
        $product = Product::findOrFail($id);
        $product->update($request->all());
        return $this->responseFormat($product, 'Success updated', 200);
    }

    public function destroy($id)
    {
        Product::findOrFail($id)->delete();
        return $this->responseFormat("deleted", 'Success deleted', 200);
    }
}
