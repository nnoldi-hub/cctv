<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Offer;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $query = trim($request->string('q')->toString());

        if (mb_strlen($query) < 2) {
            return response()->json(['clients' => [], 'offers' => [], 'tickets' => []]);
        }

        $user = $request->user();
        $canSales = $user->hasAnyRole(['admin', 'vanzari']);
        $canTechnical = $user->hasAnyRole(['admin', 'tehnic', 'suport']);

        return response()->json([
            'clients' => $canSales || $canTechnical
                ? Client::where('name', 'like', "%{$query}%")
                    ->orWhere('phone', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->take(5)
                    ->get(['id', 'name', 'phone'])
                : [],
            'offers' => $canSales
                ? Offer::with('client:id,name')
                    ->where('title', 'like', "%{$query}%")
                    ->take(5)
                    ->get(['id', 'client_id', 'title', 'status'])
                : [],
            'tickets' => $canTechnical
                ? Ticket::with('client:id,name')
                    ->where('subject', 'like', "%{$query}%")
                    ->take(5)
                    ->get(['id', 'client_id', 'subject', 'status'])
                : [],
        ]);
    }
}
