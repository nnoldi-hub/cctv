<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityController extends Controller
{
    public function index(Request $request): Response
    {
        $activities = Activity::with(['client:id,name', 'assignedTo:id,name'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('assigned_to'), fn ($q) => $q->where('assigned_to', $request->integer('assigned_to')))
            ->orderByRaw("case when status = 'pending' then 0 else 1 end")
            ->orderBy('due_at')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Sales/Activities/Index', [
            'activities' => $activities,
            'filters' => $request->only('status', 'assigned_to'),
            'users' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('Sales/Activities/Create', [
            'clients' => Client::orderBy('name')->get(['id', 'name']),
            'users' => User::orderBy('name')->get(['id', 'name']),
            'preselectedClientId' => $request->integer('client_id') ?: null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Activity::create($this->validateData($request));

        return redirect()->route('sales.activities.index')->with('success', 'Activitate creata.');
    }

    public function updateStatus(Activity $activity): RedirectResponse
    {
        $activity->update([
            'status' => $activity->status === 'completed' ? 'pending' : 'completed',
            'completed_at' => $activity->status === 'completed' ? null : now(),
        ]);

        return back()->with('success', 'Status activitate actualizat.');
    }

    public function destroy(Activity $activity): RedirectResponse
    {
        $activity->delete();

        return back()->with('success', 'Activitate stearsa.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:call,email,meeting,visit,task,note'],
            'priority' => ['required', 'in:low,normal,high'],
            'status' => ['required', 'in:pending,completed,cancelled'],
            'due_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
