<?php

namespace App\Http\Controllers;

use App\Models\CategorizedMenuItem;
use Illuminate\Http\Request;
use App\Models\MenuItem;
use Inertia\Inertia;

class MenuController extends Controller
{
    /**
     * Display the Manage Menu page.
     */
    public function index()
    {
        // Fetch data and group by category
        $menuItems = CategorizedMenuItem::all()
            ->groupBy('category') // Group by 'category' field
            ->toArray(); // Convert to an array for Inertia

        return Inertia::render('ManageMenu', [
            'menuItems' => $menuItems, // Pass to Vue
            'success' => session('success'), // Optional success message
        ]);
    }

    /**
     * Update a menu item.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'price' => 'required|numeric|min:0',
            'qty' => 'required|integer|min:0',
            'availability' => 'required|string|in:Available,Unavailable',
        ]);

        $menuItem = MenuItem::findOrFail($id);
        $menuItem->update([
            'price' => $request->price,
            'qty' => $request->qty,
            'availability' => $request->availability,
        ]);

        return redirect()->back()->with('success', 'Menu item updated successfully.');
    }


    /**
     * Delete a menu item.
     */
    public function destroy($id)
    {
        // Find the menu item and delete it
        $menuItem = MenuItem::findOrFail($id);
        $menuItem->delete();

        // Return an Inertia response to indicate success
        return inertia('ManageMenu', [
            'menuItems' => MenuItem::all()->groupBy('category'),
            'success' => 'Menu item deleted successfully.',
        ]);
    }
}
