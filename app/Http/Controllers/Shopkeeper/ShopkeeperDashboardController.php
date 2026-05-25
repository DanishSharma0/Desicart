<?php

namespace App\Http\Controllers\Shopkeeper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopkeeperDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        
        $stats = [
            'total_products' => $user->products()->count(),
            'total_stock' => $user->products()->sum('stock'),
        ];

        return view('shopkeeper.dashboard', compact('stats'));
    }
}
