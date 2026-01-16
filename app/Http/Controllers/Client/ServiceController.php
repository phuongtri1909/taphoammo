<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        return view('client.pages.services.index', [
            'category' => $request->get('category', 'Dịch vụ'),
            'products' => [],
            'totalProducts' => 0,
            'sortBy' => $request->get('sort', 'popular'),
            'filters' => $request->get('filters', []),
            'filterOptions' => []
        ]);
    }
}