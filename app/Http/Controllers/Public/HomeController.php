<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\SitePackage;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        $packages = SitePackage::where('active', true)->orderBy('sort_order')->orderBy('name')->get();

        return Inertia::render('Public/Home', [
            'packages' => $packages->isNotEmpty() ? $packages : config('packages.tiers'),
            'latestPosts' => Post::published()->latest('published_at')->take(3)->get([
                'title', 'slug', 'excerpt', 'published_at',
            ]),
        ]);
    }
}
