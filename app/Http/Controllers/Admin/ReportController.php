<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Installation;
use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Offer;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function __invoke(): Response
    {
        $months = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i)->startOfMonth());

        $revenueByMonth = $months->map(function ($month) {
            return [
                'month' => $month->format('M Y'),
                'amount' => (float) Invoice::where('status', 'paid')
                    ->whereBetween('paid_at', [$month, $month->copy()->endOfMonth()])
                    ->sum('amount'),
            ];
        });

        $offersByMonth = $months->map(function ($month) {
            return [
                'month' => $month->format('M Y'),
                'count' => Offer::whereBetween('created_at', [$month, $month->copy()->endOfMonth()])->count(),
            ];
        });

        return Inertia::render('Admin/Reports/Index', [
            'sales' => [
                'totalClients' => Client::count(),
                'newLeadsThisMonth' => Client::where('status', 'lead')->whereMonth('created_at', now()->month)->count(),
                'offersByStatus' => Offer::select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status'),
                'conversionRate' => $this->conversionRate(),
                'offersByMonth' => $offersByMonth,
            ],
            'technical' => [
                'installationsByStatus' => Installation::select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status'),
                'ticketsByStatus' => Ticket::select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status'),
                'ticketsByPriority' => Ticket::select('priority', DB::raw('count(*) as total'))->groupBy('priority')->pluck('total', 'priority'),
            ],
            'financial' => [
                'revenueByMonth' => $revenueByMonth,
                'unpaidTotal' => (float) Invoice::whereIn('status', ['unpaid', 'overdue'])
                    ->selectRaw('COALESCE(SUM(amount - paid_amount), 0) as total')
                    ->value('total'),
                'paidTotal' => (float) Invoice::where('status', 'paid')->sum('amount'),
                'overdueCount' => Invoice::where('status', 'overdue')->count(),
                'expensesTotal' => (float) Expense::sum('amount'),
                'expensesByCategory' => Expense::select('category', DB::raw('sum(amount) as total'))->groupBy('category')->pluck('total', 'category'),
                'expensesBySupplier' => Expense::query()
                    ->leftJoin('suppliers', 'suppliers.id', '=', 'expenses.supplier_id')
                    ->selectRaw("COALESCE(suppliers.name, 'Fara furnizor') as supplier, SUM(expenses.amount) as total")
                    ->groupBy('suppliers.name')
                    ->orderByDesc('total')
                    ->limit(8)
                    ->pluck('total', 'supplier'),
            ],
        ]);
    }

    private function conversionRate(): float
    {
        $total = Client::count();

        if ($total === 0) {
            return 0;
        }

        return round((Client::where('status', 'client')->count() / $total) * 100, 1);
    }
}
