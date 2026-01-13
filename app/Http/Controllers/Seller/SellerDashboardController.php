<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;

class SellerDashboardController extends Controller
{
    public function index()
    {
        return view('seller.pages.dashboard');
    }
}