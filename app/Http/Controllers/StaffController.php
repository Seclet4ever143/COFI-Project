<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function manageOrders(Request $request)
    {
        // Staff-specific logic to manage orders
        return view('staff.manageOrders');
    }

    public function viewFeedback(Request $request)
    {
        // Staff-specific logic to view feedback
        return view('staff.viewFeedback');
    }

    public function viewStaffAccount(Request $request)
    {
        // Staff-specific logic to view own account
        return view('staff.viewAccount');
    }

    public function processPayments(Request $request)
    {
        // Staff-specific logic to process payments
        return view('staff.processPayments');
    }
}
