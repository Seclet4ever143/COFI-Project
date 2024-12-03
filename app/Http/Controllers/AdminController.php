<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function manageMenu(Request $request)
    {
        // Admin-specific logic to manage the menu
        return view('admin.manageMenu');
    }

    public function manageOrders(Request $request)
    {
        // Admin-specific logic to manage orders
        return view('admin.manageOrders');
    }

    public function viewReports(Request $request)
    {
        // Admin-specific logic to view reports
        return view('admin.viewReports');
    }

    public function manageInventory(Request $request)
    {
        // Admin-specific logic to manage inventory
        return view('admin.manageInventory');
    }

    public function managePromotions(Request $request)
    {
        // Admin-specific logic to manage promotions
        return view('admin.managePromotions');
    }

    public function viewUsers(Request $request)
    {
        // Admin-specific logic to view users
        return view('admin.viewUsers');
    }

    public function viewAccount(Request $request)
    {
        // Admin-specific logic to view account
        return view('admin.viewAccount');
    }
}
