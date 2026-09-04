<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmsLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SmsLogController extends Controller
{
    public function __invoke(Request $request): Response
    {
        return Inertia::render('Admin/SmsLogs/Index', [
            'logs' => SmsLog::query()
                ->when($request->string('status')->toString(), fn ($query, $status) => $query->where('status', $status))
                ->latest()
                ->paginate(20)
                ->withQueryString(),
            'filters' => $request->only('status'),
            'driver' => config('services.sms.driver', 'log'),
        ]);
    }
}
