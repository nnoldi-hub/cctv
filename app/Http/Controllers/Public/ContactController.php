<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Notifications\NewLeadReceived;
use App\Services\SmsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class ContactController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Public/Contact');
    }

    public function store(Request $request, SmsService $sms): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $client = Client::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'city' => $data['city'] ?? null,
            'notes' => $data['notes'] ?? null,
            'source' => 'web',
            'status' => 'lead',
        ]);

        $recipients = Role::findByName('admin')->users()->get()
            ->merge(Role::findByName('vanzari')->users()->get())
            ->unique('id');

        Notification::send($recipients, new NewLeadReceived($client));

        foreach ($recipients->whereNotNull('phone') as $recipient) {
            $sms->send($recipient->phone, "Lead nou: {$client->name} ({$client->phone}).");
        }

        return back()->with('success', 'Cererea ta a fost trimisa. Te vom contacta in cel mai scurt timp.');
    }
}
