<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CustomerController extends Controller
{
    use AccountTrait;

    public function contact(){
        return Inertia::render('Customer/Contact');
    }
    
    public function index()
    {
        // Retrieve all products
        $products = Product::all();
        $category = Category::all();
        // Return Inertia response, passing data to the Vue component
        return Inertia::render('Customer/Menu', [
            'products' => $products,
            'category' => $category,
        ]);
    }

    public function display()
    {
        // Retrieve all products
        $products = Product::all();
        $category = Category::all();
        // Return Inertia response, passing data to the Vue component
        return Inertia::render('Customer/CustomerDashboard', [
            'products' => $products,
            'category' => $category,
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        $products = Product::where('name', 'like', '%' . $query . '%')->get();

        return response()->json($products);
    }

    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1'
        ]);

        $userId = Auth::id();
        $productId = $validated['id'];
        $quantity = $validated['qty'];

        // Check if the item already exists in the cart
        $cart = DB::table('carts')
            ->where('user_id', $userId)
            ->first();

        if ($cart) {

            $item = DB::table('cart_items')
                ->where('product_id', $productId)
                ->where('cart_id', $cart->id)
                ->first();


            if ($item) {
                // Update the quantity if the item exists
                DB::table('cart_items')
                    ->where('product_id', $productId)
                    ->where('cart_id', $cart->id)
                    ->update(['quantity' => $item->quantity + $quantity]);
            } else {
                // Insert a new cart item if the product is not in the cart
                DB::table('cart_items')->insert([
                    'cart_id' => $cart->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                ]);
            }


            // Update the quantity if the item exists

        } else {
            // Insert a new item into the cart
            $cartId = DB::table('carts')->insertGetId([
                'user_id' => $userId,
            ]);

            DB::table('cart_items')->insert([
                'cart_id' => $cartId,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }

        return redirect()->route('menu');
    }



    public function getCartsWithProducts()
    {
        $cartsWithProducts = DB::table('cart_view')
            ->where('user_id', Auth::id())
            ->get();
        return Inertia::render('Customer/Cart', [
            'cart' => $cartsWithProducts,
        ]);
    }

    public function destroy($itemId)
    {
        // Check if the item exists in the cart
        $item = DB::table('cart_items')->where('id', $itemId)->first();

        if (!$item) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        // Delete the item from the database
        DB::table('cart_items')->where('id', $itemId)->delete();

    }




    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $request->validate([
            'quantity' => 'required|integer|min:1', // Ensure quantity is valid
        ]);

        // Fetch the cart item using DB facade
        $cartItem = DB::table('cart_items')->where('id', $id)->first();

        // Check if the cart item existsx   
        if (!$cartItem) {
            return response()->json(['message' => 'Cart item not found'], 404);
        }

        // Update the cart item quantity in the database
        DB::table('cart_items')
            ->where('id', $id)
            ->update([
                'quantity' => $request->quantity,
                'updated_at' => now(),
            ]);

        // Recalculate the updated price (assuming 'product_id' is available)
        $product = DB::table('products')->where('id', $cartItem->product_id)->first();

        $updatedPrice = $product->price * $request->quantity;
        return response()->json([
            'message' => 'Quantity updated successfully',
            'quantity' => $request->quantity,
            'updated_price' => $updatedPrice,
        ]);
    }
    public function submitPayment(Request $request)
    {
        $validated = $request->validate([
            'paymentMethod' => 'required|string',
            'gcashNumber' => 'nullable|string',
            'total' => 'required|numeric|min:0',
        ]);
        $userId = Auth::id(); // Get authenticated user's ID

        try {
            DB::beginTransaction();
            $cartItems = DB::table('cart_items')
                ->join('carts', 'cart_items.cart_id', '=', 'carts.id')
                ->where('carts.user_id', $userId)
                ->get();

            if ($cartItems->isEmpty()) {
                throw new \Exception('No items found in the cart for this user.');
            }

            $transactionId = DB::table('transactions')->insertGetId([
                'user_id' => $userId,
                'total_amount' => $validated['total'],
                'gcash_no' => $validated['gcashNumber'],
                'payment_method' => $validated['paymentMethod'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            foreach ($cartItems as $cartItem) {
                DB::table('transaction_items')->insert([
                    'transaction_id' => $transactionId,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'total_amount' => $validated['total'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            // DB::table('transaction_items')->insert([
            //     'transaction_id' => $transactionId,
            //     'product_id' => $cartItems->product_id,
            //     'quantity' => $cart_items->quantity,
            //     'total_amount' => $transactionId->total_amount,
            // ]);
            // Insert the transaction record

            // Clear the user's cart
            DB::table('cart_items')
                ->whereIn('cart_items.cart_id', function ($query) use ($userId) {
                    $query->select('id')
                        ->from('carts')
                        ->where('user_id', $userId);
                })
                ->delete();


            DB::commit();

            return Inertia::render('Customer/Cart');
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Payment failed, please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function indexTransaction()
    {
        $cartsWithProducts = DB::table('transaction_summary')
            ->where('user_id', Auth::id())
            ->get();
        return Inertia::render('Customer/Transactions', [
            'transactions' => $cartsWithProducts,
        ]);
    }

    /**
     * Store a new transaction.
     */
    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'paymentMethod' => 'required|string',
    //         'gcashNumber' => 'nullable|string',
    //         'total' => 'required|numeric',
    //     ]);

    //     // Save the transaction, for example:
    //     Transaction::create($data);

    //     return response()->json($data); // Send a response back to the frontend
    // }

    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'payment_method' => 'required|string',
            'gcash_no' => 'nullable|string',
            'total' => 'required|numeric',
        ]);
    
        // Create the transaction or handle payment logic
        $transaction = DB::table('transactions')->insert([
            'user_id' => $validated['user_id'],
            'payment_method' => $validated['payment_method'],
            'gcash_no' => $validated['gcash_no'],
            'total' => $validated['total'],
        ]);
    
        return response()->json(['message' => 'Transaction successful!', 'transaction' => $transaction]);
    }
    


    /**
     * Display the specified transaction.
     */
    public function show($id)
    {
        $transaction = Transaction::where('user_id', Auth::id())->findOrFail($id);
        return response()->json($transaction);
    }

    /**
     * Update the specified transaction.
     */
    public function updateTransaction(Request $request, $id)
    {
        $transaction = Transaction::where('user_id', Auth::id())->findOrFail($id);

        $validatedData = $request->validate([
            'gcash_no' => 'nullable|string|max:20',
            'status' => 'in:pending,completed,canceled',
        ]);

        $transaction->update($validatedData);

        return response()->json(['message' => 'Transaction updated successfully', 'transaction' => $transaction]);
    }

    /**
     * Remove the specified transaction.
     */
    public function destroyTransaction($id)
    {
        $transaction = Transaction::where('user_id', Auth::id())->findOrFail($id);
        $transaction->delete();

        return response()->json(['message' => 'Transaction deleted successfully']);
    }
}
