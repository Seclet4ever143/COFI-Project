<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerController extends Controller
{
    use AccountTrait;

    public function placeOrder(Request $request)
    {
        // Customer-specific logic to place an order
        return view('customer.placeOrder');
    }

    public function viewFeedback(Request $request)
    {
        // Customer-specific logic to view feedback
        return view('customer.viewFeedback');
    }

    public function viewCustomerAccount(Request $request)
    {
        // Customer-specific logic to view own account
        return view('customer.viewAccount');
    }

    public function processPayments(Request $request)
    {
        // Customer-specific logic to process payments
        return view('customer.processPayments');
    }
}
