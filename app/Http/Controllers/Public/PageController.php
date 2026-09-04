<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\SitePackage;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function about(): Response
    {
        return Inertia::render('Public/About');
    }

    public function services(): Response
    {
        $packages = SitePackage::where('active', true)->orderBy('sort_order')->orderBy('name')->get();

        return Inertia::render('Public/Services', [
            'packages' => $packages->isNotEmpty() ? $packages : config('packages.tiers'),
        ]);
    }
}
