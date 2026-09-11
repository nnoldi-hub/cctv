<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\ShopOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShopOrderController extends Controller
{
    public function index(Request $request): Response
    {
        $orders = ShopOrder::query()
            ->with('client:id,name')
            ->when($request->string('search')->toString(), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($request->string('status')->toString(), fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Shop/Orders/Index', [
            'orders' => $orders,
            'filters' => $request->only('search', 'status'),
            'summary' => [
                'new' => ShopOrder::where('status', 'new')->count(),
                'confirmed' => ShopOrder::where('status', 'confirmed')->count(),
                'totalRevenue' => (float) ShopOrder::whereNotIn('status', ['cancelled'])->sum('total'),
            ],
        ]);
    }

    public function show(ShopOrder $shopOrder): Response
    {
        $shopOrder->load(['items', 'client:id,name', 'invoice']);

        return Inertia::render('Admin/Shop/Orders/Show', [
            'order' => $shopOrder,
        ]);
    }

    public function updateStatus(Request $request, ShopOrder $shopOrder): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:new,confirmed,shipped,completed,cancelled'],
        ]);

        $shopOrder->update($data);

        if ($data['status'] === 'completed' && ! $shopOrder->invoice_id && $shopOrder->client_id) {
            $this->createInvoiceForOrder($shopOrder);
        }

        return back()->with('success', 'Status comanda actualizat.');
    }

    public function applyManualDiscount(Request $request, ShopOrder $shopOrder): RedirectResponse
    {
        $data = $request->validate([
            'manual_discount' => ['required', 'numeric', 'min:0'],
        ]);

        $netSubtotal = max((float) $shopOrder->subtotal - (float) $shopOrder->discount_total - (float) $data['manual_discount'], 0);
        $total = round($netSubtotal + (float) $shopOrder->shipping_cost, 2);

        $shopOrder->update([
            'manual_discount' => $data['manual_discount'],
            'total' => $total,
        ]);

        return back()->with('success', 'Reducere aplicata comenzii.');
    }

    public function generateInvoice(ShopOrder $shopOrder): RedirectResponse
    {
        abort_if($shopOrder->invoice_id, 422, 'Comanda are deja o factura asociata.');
        abort_if(! $shopOrder->client_id, 422, 'Comanda nu are un client asociat, nu se poate genera factura.');

        $this->createInvoiceForOrder($shopOrder);

        return back()->with('success', 'Factura generata pentru comanda.');
    }

    private function createInvoiceForOrder(ShopOrder $shopOrder): Invoice
    {
        $invoice = Invoice::create([
            'client_id' => $shopOrder->client_id,
            'invoice_number' => Invoice::nextInvoiceNumber(),
            'amount' => $shopOrder->total,
            'status' => 'unpaid',
            'issued_at' => now(),
            'due_at' => now()->addDays(5),
        ]);

        $shopOrder->update(['invoice_id' => $invoice->id]);

        return $invoice;
    }

    public function destroy(ShopOrder $shopOrder): RedirectResponse
    {
        $shopOrder->delete();

        return redirect()->route('admin.shop-orders.index')->with('success', 'Comanda stearsa.');
    }
}
