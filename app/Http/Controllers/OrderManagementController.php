<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class OrderManagementController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/OrderManagement');
    }
}
