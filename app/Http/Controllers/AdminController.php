<?php

namespace App\Http\Controllers;

use App\Models\ActivityLogs;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Redirect;

class AdminController extends Controller
{   

    public function viewWelcome()
    {
        // Retrieve all products
        $products = Product::all();
        $category = Category::all();
        // Return Inertia response, passing data to the Vue component
        return Inertia::render('Menu', [
            'products' => $products,
            'category' => $category,
        ]);
    }
    public function viewMenu()
    {
        
        // Fetch data from the `products` table and group it by category
        $menuItems = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('products.*', 'categories.name as category')
            ->get()
            ->groupBy('category')
            ->toArray();

        return Inertia::render('Admin/ManageMenu', [
            'menuItems' => $menuItems,
            'success' => session('success'),
        ]);
    }

    public function updateMenu(Request $request, $id)
    {
        
        $request->validate([
            'price' => 'required|numeric|min:0',
            'qty' => 'required|integer|min:0',
            'availability' => 'required|boolean',
        ]);

        $affectedRows = DB::table('products')
            ->where('id', $id)
            ->update([
                'price' => $request->price,
                'qty' => $request->qty,
                'availability' => $request->availability,
                'updated_by' => auth()->id(),
            ]);

        if ($affectedRows) {
            return redirect()->back()->with('success', 'Menu item updated successfully.');
        }

        return redirect()->back()->withErrors('Failed to update menu item.');
    }   

    public function deleteMenuProduct($id)
    {
        try {
            $deleted = DB::table('products')
                ->where('id', $id)
                ->delete();

            if ($deleted) {
                return redirect()->back()->with('success', 'Menu item deleted successfully.');
            }

            return redirect()->back()->withErrors('Failed to delete menu item.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('An error occurred: ' . $e->getMessage());
        }
    }

    public function viewAccountManagement()
    {
        return Inertia::render('Admin/AccountManagement');
    }

    public function insertIntoAccountManagement(Request $request)
{
    // Validate the request data
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email', // Unique email check
        'phone' => 'required|string|max:11|unique:users,phone', // Unique phone check
        'password' => 'required|string|min:8',
        'role_id' => 'required|exists:role,id', // Validate role_id against the roles table
    ], [
        // Custom error messages
        'email.unique' => 'The email address is already registered.',
        'phone.unique' => 'The phone number is already registered.',
    ]);

    // Insert the new user into the users table
    DB::table('users')->insert([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'phone' => $validated['phone'],
        'password' => Hash::make($validated['password']),
        'role_id' => $validated['role_id'], 
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Return a success response
    return response()->json(['message' => 'Account created successfully!'], 201);
}

    

    public function displayUsers()
    {
        // Use DB queries to fetch users grouped by role_id
        $users = [
            'admin' => DB::table('users')->where('role_id', 1)->get(),
            'staff' => DB::table('users')->where('role_id', 2)->get(),
            'customer' => DB::table('users')->where('role_id', 3)->get(),
        ];

        return response()->json($users);
    }
    
    public function updateUsers(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|string|email|max:255|unique:users,email,$id", // Ensure the current user's email is not considered unique
            'phone' => "required|string|max:11|unique:users,phone,$id", // Ensure current phone is not considered unique
            'role_id' => 'required|exists:role,id', // Validate role_id exists in roles table
        ]);

        // Update the user data using raw DB query
        DB::table('users')->where('id', $id)->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role_id' => $validated['role_id'], // Use role_id directly
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Account updated successfully!']);
    }

    public function destroyUsers($id)
    {
        try {
            // Log before making any in
        
    
            // Update related products
            $productsUpdated = DB::table('products')->where('updated_by', $id)->update(['updated_by' => null]);
            Log::info("Updated products related to user $id: $productsUpdated");
            //dd($id);
            // Delete the user
            $userDeleted = DB::table('users')
                ->where('id', $id)
                ->delete();
            //Log::info("Deleted user with ID: $id: $userDeleted");
    
            return back()->with('success', 'Account deleted successfully!');
        } catch (\Exception $e) {
            // Log the error message
            Log::error("Error deleting user with ID: $id - " . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete account.']);
        }
    }



    public function viewProductManagement()
    {
        return Inertia::render('Admin/ProductManagement');
    }

    public function displayProducts()
    {
        $products = DB::table('products')->get();
        $categories = DB::table('categories')->pluck('name', 'id');
        return response()->json([
            'products' => $products,
            'categories' => $categories,
        ]);
    }   

    public function insertProducts(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'qty' => 'required|integer|min:0',
                'category_id' => 'required|integer|exists:categories,id',
                'description' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'availability' => 'required|boolean',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $imagePath = $request->file('image') ? $request->file('image')->store('images', 'public') : null;

        DB::table('products')->insert([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'qty' => $validated['qty'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'],
            'image' => $imagePath,
            'availability' => $validated['availability'],
            'updated_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('Admin/ProductManagement')->with('success', 'Add Product Successfully');
    }

   

    public function updateProducts(Request $request, $id)
    {

        try {

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'qty' => 'required|integer|min:0',
                'category_id' => 'required|integer|exists:categories,id',
                'description' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'availability' => 'required|boolean',

            ]);
            \Log::info('Validated data:', $validated);


    
            // Ensure availability is a Boolean
            $validated['availability'] = filter_var($validated['availability'], FILTER_VALIDATE_BOOLEAN);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed:', $e->errors());
            return response()->json(['errors' => $e->errors()], 422);
        }
    
        // Check if the product exists
        $product = DB::table('products')->where('id', $id)->first();
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }
    
        // Handle image upload
        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store('images', 'public');
        }
        
        
        $validated['availability'] = (bool) $validated['availability'];


        // Update the product
        DB::table('products')->where('id', $id)->update([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'qty' => $validated['qty'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'],
            'image' => $imagePath,
            'availability' => $validated['availability'],
            'updated_by' => auth()->id(),
            'updated_at' => now(),
        ]);
    
        return redirect('Admin/ProductManagement')->with('success', 'Product updated successfully');
    }
    




    public function deleteProduct($id)
    {
        $product = DB::table('products')->where('id', $id)->first();
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        DB::table('products')->where('id', $id)->delete();

        return response()->json(['success' => 'Product deleted successfully']);
    }

    public function viewOrder()
    {

        $orders = DB::select('SELECT * FROM transaction_summary_admin');
        $details = DB::select('SELECT * FROM order_products');
        return Inertia::render('Admin/OrderManagement', [
            'orders' => $orders,
            'details' => $details,
        ]);     
       // return Inertia::render('Admin/OrderManagement');
    }


    public function displayOrder()
    {
        try {
            $orders = DB::table('orders')
                ->select('orders.*', 'users.name as customer_name', 'staff.name as staff_name')
                ->join('users', 'orders.customer_id', '=', 'users.id')
                ->leftJoin('users as staff', 'orders.staff_id', '=', 'staff.id')
                ->orderBy('orders.created_at', 'desc')
                ->get();
            
            return Inertia::render('Admin/OrderManagement', [
                'orders' => $orders
            ]);
        } catch (\Exception $e) {
            // Log the error and return a custom message
            Log::error('Error fetching orders: ' . $e->getMessage());
            return response()->json(['error' => 'There was an error fetching the orders.'], 500);
        }
    }
    
    
    public function updateOrder(Request $request, $id)
    {
        $status = $request->input('status');
        
        DB::table('transactions')
            ->where('id', $id)
            ->update(['status' => $status]);

        return Redirect::back()->with('success','Update order successfully!');
    }

    public function getOrderHistory()
    {
        $orders = DB::select('SELECT * FROM transaction_summary_admin');

        return Inertia::render('Admin/OrderManagement', [
            'orders' => $orders,
        ]);
    }

    public function deleteOrder($id)
    {
        // Check if the order exists
        $order = DB::table('transactions')->where('id', $id)->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Delete the order
        DB::table('transactions')->where('id', $id)->delete();

        return Inertia::location(route('ordermanagement'));

        // Return a success response
    }

    public function acceptOrder(Request $request){
        
        $validated = $request->validate([
            'id' => 'required|numeric'
        ]);

        $items = DB::table('transactions')
            ->where('id', $validated['id'])
            ->update(['status' => 'Processing']);

        return Inertia::location('/Admin/OrderManagement');
    }

    public function shipOrder(Request $request){
        
        $validated = $request->validate([
            'id' => 'required|numeric'
        ]);

        $items = DB::table('transactions')
            ->where('id', $validated['id'])
            ->update(['status' => 'Shipped']);

    return Inertia::location('/Admin/OrderManagement');
    }


    public function completeOrder(Request $request){
        
        $validated = $request->validate([
            'id' => 'required|numeric'
        ]);

        $items = DB::table('transactions')
            ->where('id', $validated['id'])
            ->update(['status' => 'Completed']);

    return Inertia::location('/Admin/OrderManagement');
    }

public function indexLogs()
{                       
        // Retrieve all activity logs ordered by creation date (most recent first)
        $activityLogs = ActivityLogs::orderBy('created_at', 'desc')->get();

        return response()->json($activityLogs);
    }

    /**
     * Fetch a specific activity log by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showLogs($id)
    {
        // Retrieve a specific activity log by ID
        $activityLog = ActivityLogs::find($id);

        if (!$activityLog) {
            return response()->json(['message' => 'Activity log not found.'], 404);
        }

        return response()->json($activityLog);
    }

    public function userDestroy($id)
    {
        // Attempt to delete the user using the DB Facade
        $deleted = DB::table('users')
            ->where('id', $id)
            ->delete();

        if (!$deleted) {
            // If no rows were deleted (user not found)
            return redirect()->route('accountmanagement')->with('error', 'User not found');
        }

        // If the user was deleted successfully
        return redirect()->route('accountmanagement')->with('error', 'User deleted successfully');
    }
}



