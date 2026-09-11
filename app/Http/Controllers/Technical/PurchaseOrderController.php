<?php

namespace App\Http\Controllers\Technical;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Expense;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PurchaseOrderController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Technical/PurchaseOrders/Index', [
            'orders' => PurchaseOrder::with('supplier:id,name')->withCount('items')->latest()->paginate(15),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Technical/PurchaseOrders/Create', [
            'suppliers' => Supplier::orderBy('name')->get(['id', 'name']),
            'equipment' => Equipment::where('is_active', true)->orderBy('name')->get(['id', 'name', 'sku', 'cost_price', 'unit']),
        ]);
    }

    public function replenish(): RedirectResponse
    {
        $equipment = Equipment::query()
            ->with('supplier')
            ->where('is_active', true)
            ->whereColumn('stock_quantity', '<=', 'minimum_stock')
            ->whereNotNull('supplier_id')
            ->get();

        $created = 0;
        DB::transaction(function () use ($equipment, &$created) {
            foreach ($equipment->groupBy('supplier_id') as $supplierId => $items) {
                $lines = $items->map(fn (Equipment $item) => [
                    'equipment_id' => $item->id,
                    'quantity' => max(((int) $item->minimum_stock * 2) - (int) $item->stock_quantity, 1),
                    'unit_cost' => $item->cost_price ?? 0,
                ])->values()->all();
                $order = PurchaseOrder::create([
                    'supplier_id' => $supplierId,
                    'order_number' => 'PO-'.now()->format('YmdHis').'-'.random_int(10, 99),
                    'status' => 'draft',
                    'notes' => 'Generata automat din recomandarea de reaprovizionare.',
                    'total_amount' => collect($lines)->sum(fn (array $line) => $line['quantity'] * $line['unit_cost']),
                ]);
                $order->items()->createMany($lines);
                $created++;
                AuditLog::record(request()->user(), 'purchase_order.replenishment_created', "Comanda recomandata {$order->order_number} a fost generata.", $order, ['items_count' => count($lines)]);
            }
        });

        return back()->with('success', $created
            ? "Au fost generate {$created} comenzi recomandate."
            : 'Nu exista materiale sub prag cu furnizor asociat.');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'ordered_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.equipment_id' => ['required', 'exists:equipment,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
        ]);

        $order = DB::transaction(function () use ($data) {
            $order = PurchaseOrder::create([
                'supplier_id' => $data['supplier_id'],
                'order_number' => 'PO-'.now()->format('YmdHis').'-'.random_int(10, 99),
                'status' => 'ordered',
                'ordered_at' => $data['ordered_at'] ?? today(),
                'notes' => $data['notes'] ?? null,
                'total_amount' => collect($data['items'])->sum(fn ($item) => $item['quantity'] * $item['unit_cost']),
            ]);
            $order->items()->createMany($data['items']);
            AuditLog::record(
                request()->user(),
                'purchase_order.created',
                "Comanda {$order->order_number} a fost creata.",
                $order,
                ['total_amount' => (float) $order->total_amount, 'items_count' => count($data['items'])],
            );
            return $order;
        });

        return redirect()->route('technical.purchase-orders.index')->with('success', "Comanda {$order->order_number} a fost creata.");
    }

    public function receive(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        abort_unless($purchaseOrder->status !== 'received', 422, 'Comanda este deja receptionata.');

        $data = $request->validate(['quantities' => ['nullable', 'array'], 'quantities.*' => ['numeric', 'min:0']]);
        DB::transaction(function () use ($purchaseOrder, $data) {
            $purchaseOrder->load('items.equipment');
            $receivedTotal = 0;
            foreach ($purchaseOrder->items as $item) {
                $remaining = (float) $item->quantity - (float) $item->received_quantity;
                $quantity = array_key_exists($item->id, $data['quantities'] ?? [])
                    ? min($remaining, (float) $data['quantities'][$item->id])
                    : $remaining;
                if ($quantity > 0) {
                    $item->equipment->increment('stock_quantity', $quantity);
                    $item->equipment->update(['cost_price' => $item->unit_cost]);
                    $item->increment('received_quantity', $quantity);
                    $receivedTotal += $quantity * (float) $item->unit_cost;
                }
            }
            if ($receivedTotal > 0) {
                Expense::create([
                'supplier_id' => $purchaseOrder->supplier_id,
                'description' => 'Comanda '.$purchaseOrder->order_number,
                'category' => 'material',
                'amount' => $receivedTotal,
                'expense_date' => today(),
                'document_number' => $purchaseOrder->order_number,
                'notes' => 'Generata automat la receptionarea comenzii.',
                ]);
            }
            $complete = $purchaseOrder->items()->whereColumn('received_quantity', '<', 'quantity')->doesntExist();
            $purchaseOrder->update(['status' => $complete ? 'received' : 'partially_received', 'received_at' => $complete ? today() : null]);
            AuditLog::record(
                request()->user(),
                'purchase_order.received',
                "Recepție inregistrata pentru comanda {$purchaseOrder->order_number}.",
                $purchaseOrder,
                ['status' => $purchaseOrder->status, 'amount' => $receivedTotal],
            );
        });

        return back()->with('success', 'Recepția a fost înregistrată. Stocul și cheltuielile au fost actualizate.');
    }
}
