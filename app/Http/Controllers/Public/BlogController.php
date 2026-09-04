<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Public/Blog/Index', [
            'posts' => Post::published()->latest('published_at')->paginate(9)->withQueryString(),
        ]);
    }

    public function show(string $slug): Response
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();

        $related = Post::published()
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->take(3)
            ->get(['title', 'slug', 'excerpt', 'published_at']);

        return Inertia::render('Public/Blog/Show', [
            'post' => $post,
            'related' => $related,
        ]);
    }
}
