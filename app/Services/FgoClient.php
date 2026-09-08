<?php

namespace App\Services;

use App\Models\Invoice;
use RuntimeException;

class FgoClient
{
    public function isConfigured(): bool
    {
        return (bool) config('services.fgo.enabled')
            && filled(config('services.fgo.base_url'))
            && filled(config('services.fgo.token'));
    }

    public function configurationMessage(): string
    {
        if (! config('services.fgo.enabled')) {
            return 'Integrarea FGO este dezactivata.';
        }

        if (! filled(config('services.fgo.base_url')) || ! filled(config('services.fgo.token'))) {
            return 'Integrarea FGO nu este configurata. Adauga FGO_BASE_URL si FGO_TOKEN in .env.';
        }

        return 'Integrarea FGO este configurata.';
    }

    public function syncInvoice(Invoice $invoice): never
    {
        throw new RuntimeException(
            'Conectorul FGO este pregatit, dar endpoint-urile API trebuie confirmate din contul FGO inainte de emiterea facturilor.'
        );
    }
}
