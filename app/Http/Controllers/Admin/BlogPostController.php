<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class BlogPostController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Blog/Index', ['posts' => Post::latest()->get()]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Blog/Edit', ['post' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        Post::create($this->validateData($request));

        return redirect()->route('admin.blog.index')->with('success', 'Articolul a fost creat.');
    }

    public function edit(Post $post): Response
    {
        return Inertia::render('Admin/Blog/Edit', ['post' => $post]);
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $post->update($this->validateData($request, $post));

        return redirect()->route('admin.blog.index')->with('success', 'Articolul a fost actualizat.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        return back()->with('success', 'Articolul a fost sters.');
    }

    private function validateData(Request $request, ?Post $post = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/', 'unique:posts,slug,'.($post?->id ?? 'NULL')],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'cover_image' => ['nullable', 'image', 'max:2048'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('blog', 'public');
        }
        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        return $data;
    }
}
