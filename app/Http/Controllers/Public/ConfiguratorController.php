<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Inertia\Inertia;
use Inertia\Response;

class ConfiguratorController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Public/Configurator', [
            'cameras' => Equipment::where('category', 'camera')->orderBy('unit_price')->get(['id', 'name', 'unit_price']),
            'nvrs' => Equipment::where('category', 'nvr')->orderBy('unit_price')->get(['id', 'name', 'unit_price']),
            'storage' => Equipment::where('category', 'accessory')->where('sku', 'like', 'HDD-%')->orderBy('unit_price')->get(['id', 'name', 'unit_price']),
            'laborPerCamera' => (float) (Equipment::where('sku', 'LABOR-CAM')->value('unit_price') ?? 0),
        ]);
    }

    public function cable(): Response
    {
        $cable = Equipment::where('sku', 'CBL-UTP6')->first();
        $connector = Equipment::where('sku', 'CON-RJ45')->first();

        return Inertia::render('Public/CableCalculator', [
            'cablePricePerMeter' => (float) ($cable->unit_price ?? 0),
            'connectorPrice' => (float) ($connector->unit_price ?? 0),
        ]);
    }
}
