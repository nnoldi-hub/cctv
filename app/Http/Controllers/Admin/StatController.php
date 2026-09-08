<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StatController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Stats/Index', ['stats' => Stat::orderBy('key')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Stat::create($this->validateData($request));

        return back()->with('success', 'Statistica a fost adaugata.');
    }

    public function update(Request $request, Stat $stat): RedirectResponse
    {
        $stat->update($this->validateData($request, $stat));

        return back()->with('success', 'Statistica a fost actualizata.');
    }

    public function destroy(Stat $stat): RedirectResponse
    {
        $stat->delete();

        return back()->with('success', 'Statistica a fost stearsa.');
    }

    private function validateData(Request $request, ?Stat $stat = null): array
    {
        return $request->validate([
            'key' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9_]+$/', 'unique:stats,key,'.($stat?->id ?? 'NULL')],
            'value' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:50'],
            'is_dynamic' => ['boolean'],
        ]);
    }
}
