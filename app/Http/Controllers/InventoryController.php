<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MenuItem;
use App\Models\Order;

class InventoryController extends Controller
{
    // Display inventory and pending orders
    public function index()
    {
        return inertia('Inventory', [
            'menuItems' => MenuItem::all(),
            'pendingOrders' => Order::where('status', 'pending')->get(),
        ]);
    }

    // Update inventory item
    public function update(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:menu_items,id',
            'quantity' => 'required|integer',
            'availability' => 'required|boolean',
        ]);

        $menuItem = MenuItem::find($data['id']);
        $menuItem->update($data);

        return redirect()->back()->with('message', 'Inventory updated successfully.');
    }

    // Accept a pending order
    public function acceptOrder(Order $order)
    {
        $order->update(['status' => 'accepted']);
        return redirect()->back()->with('message', 'Order accepted.');
    }

    // Decline a pending order
    public function declineOrder(Order $order)
    {
        $order->update(['status' => 'declined']);
        return redirect()->back()->with('message', 'Order declined.');
    }
}
