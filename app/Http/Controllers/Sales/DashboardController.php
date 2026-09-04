<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Offer;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Sales/Dashboard', [
            'stats' => [
                'clients' => Client::count(),
                'leads' => Client::where('status', 'lead')->count(),
                'offers' => Offer::count(),
                'offersAccepted' => Offer::where('status', 'accepted')->count(),
            ],
            'recentLeads' => Client::where('status', 'lead')->latest()->take(5)->get(['id', 'name', 'phone', 'source', 'created_at']),
            'recentOffers' => Offer::with('client:id,name')->latest()->take(5)->get(['id', 'client_id', 'title', 'status', 'total_amount', 'created_at']),
        ]);
    }
}
