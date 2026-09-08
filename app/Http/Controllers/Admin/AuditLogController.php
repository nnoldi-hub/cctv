<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuditLogController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $logs = AuditLog::with('user:id,name')
            ->forAction($request->string('action')->toString() ?: null)
            ->when($request->filled('user_id'), fn ($query) => $query->where('user_id', $request->integer('user_id')))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('Admin/AuditLogs/Index', [
            'logs' => $logs,
            'filters' => $request->only('action', 'user_id'),
            'actions' => AuditLog::query()->distinct()->orderBy('action')->pluck('action'),
            'users' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }
}
