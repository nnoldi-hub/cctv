<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('Admin/Settings/Edit', [
            'settings' => Setting::allSettings(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'company_email' => ['required', 'email', 'max:255'],
            'company_phone' => ['required', 'string', 'max:50'],
            'company_address' => ['nullable', 'string', 'max:255'],
            'company_hours' => ['required', 'string', 'max:255'],
            'social_facebook' => ['nullable', 'url', 'max:255'],
            'social_instagram' => ['nullable', 'url', 'max:255'],
            'social_linkedin' => ['nullable', 'url', 'max:255'],
            'invoice_series' => ['required', 'string', 'max:20'],
            'vat_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'minimum_profit_margin' => ['required', 'numeric', 'min:0', 'max:100'],
            'operational_reminders_email_enabled' => ['nullable', 'boolean'],
            'shop_enabled' => ['nullable', 'boolean'],
            'shop_free_shipping_threshold' => ['nullable', 'numeric', 'min:0'],
            'shop_shipping_cost' => ['nullable', 'numeric', 'min:0'],
        ]);

        $data['operational_reminders_email_enabled'] = $request->boolean('operational_reminders_email_enabled') ? '1' : '0';
        $data['shop_enabled'] = $request->boolean('shop_enabled') ? '1' : '0';
        $data['shop_free_shipping_threshold'] = $data['shop_free_shipping_threshold'] ?? Setting::get('shop_free_shipping_threshold');
        $data['shop_shipping_cost'] = $data['shop_shipping_cost'] ?? Setting::get('shop_shipping_cost');

        Setting::setMany($data);

        return back()->with('success', 'Setari actualizate.');
    }
}
