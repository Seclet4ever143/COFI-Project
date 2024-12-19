<?php

namespace App\Http\Controllers;


use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;


class StaffController extends Controller
{
    public function displayMenu(){
        // Fetch data from the `products` table and group it by category
        $menuItems = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('products.*', 'categories.name as category')
            ->get()
            ->groupBy('category')
            ->toArray();

        return Inertia::render('Staff/StaffMenuManage', [
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
            'updated_by' => auth()->id(), // Corrected here
        ]);

    if ($affectedRows) {
        return redirect()->back()->with('success', 'Menu item updated successfully.');
    }

    return redirect()->back()->withErrors('Failed to update menu item.');
}


public function viewTransactions()
{
    // Query the 'transaction_summary_with_items' view to fetch transaction data with items
    $transactions = DB::table('transaction_summary_with_items')->get();

    // Group the transaction items by transaction_id for easier frontend handling
    $groupedTransactions = $transactions->groupBy('transaction_id')->map(function ($items, $transactionId) {
        // Extract transaction-level data from the first item (common across grouped items)
        $firstItem = $items->first();

        return [
            'id' => $transactionId,
            'user_name' => $firstItem->user_name,
            'total_amount' => $firstItem->total,
            'status' => $firstItem->status,
            'created_at' => $firstItem->transaction_date,
            'transaction_items' => $items->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'item_total' => $item->item_total,
                ];
            })->toArray(),
        ];
    });

    // Return the transformed data to the Inertia view
    return Inertia::render('Staff/StaffOrderManage', [
        'transactions' => $groupedTransactions->values(), // Ensure a clean array for Inertia
    ]);
}



    public function acceptOrder(Order $order)
    {
        if ($order->status === 'Pending') {
            $order->update(['status' => 'Processing']);
            return redirect()->back()->with('success', 'Order accepted successfully.');
        }
        return redirect()->back()->with('error', 'Only pending orders can be accepted.');
    }

    public function updateOrder(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:Pending,Processing,Completed',
        ]);

        $order->update($validated);
        return redirect()->back()->with('success', 'Order updated successfully.');
    }

    public function destroyOrder(Order $order)
    {
        $order->delete();
        return redirect()->back()->with('success', 'Order deleted successfully.');
    }
    public function viewOrders()
    {
        $orders = DB::select('SELECT * FROM   transaction_summary_admin');
        $details = DB::select('SELECT * FROM order_products');

        return Inertia::render('Staff/StaffOrderManage', [
            'orders' => $orders,
            'details' => $details,
        ]);
    }

    // View order history
    // View order history
    public function getOrderHistorys()
    {
        $orders = DB::select('SELECT * FROM  transaction_summary_admin');

        return Inertia::render('Staff/StaffOrderManage', [
            'orders' => $orders,
        ]);
    }


    // Delete an order by ID
    public function deleteOrders($id)
    {
        // Check if the order exists
        $order = DB::table('transactions')->where('id', $id)->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Delete the order
        DB::table('transactions')->where('id', $id)->delete();

        return redirect()->route('staff.orders')->with('success', 'Order deleted successfully.');
    }

    // Accept an order and update its status to 'Processing'
    public function acceptOrders(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|numeric',
        ]);

        DB::table('transactions')
            ->where('id', $validated['id'])
            ->update(['status' => 'Processing']);

        return Inertia::location('/Staff/StaffOrderManage');
    }

    // Ship an order and update its status to 'Shipped'
    public function shipOrders(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|numeric',
        ]);

        DB::table('transactions')
            ->where('id', $validated['id'])
            ->update(['status' => 'Shipped']);

            return Inertia::location('/Staff/StaffOrderManage');
    }

    // Complete an order and update its status to 'Completed'
    public function completeOrders(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|numeric',
        ]);

        DB::table('transactions')
            ->where('id', $validated['id'])
            ->update(['status' => 'Completed']);

            return Inertia::location('/Staff/StaffOrderManage');
    }




}



