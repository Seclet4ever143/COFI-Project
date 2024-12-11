<?php

namespace App\Http\Controllers;


use App\Models\MenuItem;
use App\Models\Product;
use Illuminate\Http\Request;


class ProductController extends Controller
{
    public function index()
{
    $products = Product::all();
    return response()->json($products);
}

    

    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'category' => 'required|string',
        'price' => 'required|numeric',
        'qty' => 'required|integer',
    ]);

    $product = Product::create([
        'name' => $request->name,
        'category' => $request->category,
        'price' => $request->price,
        'qty' => $request->qty,
    ]);

    MenuItem::create([
        'name' => $product->name,
        'price' => $product->price,
        'qty' => $product->qty,
        'category' => $product->category,
        'product_id' => $product->id,
    ]);

    return response()->json(['message' => 'Product and Menu Item added successfully.']);
}

    


public function update(Request $request, Product $product)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'category' => 'required|string',
        'price' => 'required|numeric|min:0',
        'qty' => 'required|integer|min:0',
    ]);

    $product->update([
        'name' => $request->name,
        'category' => $request->category,
        'price' => $request->price,
        'qty' => $request->qty,
    ]);

    return response()->json(['message' => 'Product updated successfully.']);
}


    public function destroy(Product $product)
    {
        $product->delete();
    }
}
