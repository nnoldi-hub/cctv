<?php

namespace Tests\Feature;

use App\Models\Equipment;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads(): void
    {
        $this->get(route('public.home'))->assertOk();
    }

    public function test_about_page_loads(): void
    {
        $this->get(route('public.about'))->assertOk();
    }

    public function test_services_page_loads(): void
    {
        $this->get(route('public.services'))->assertOk();
    }

    public function test_configurator_page_loads(): void
    {
        Equipment::factory()->create(['category' => 'camera']);
        Equipment::factory()->create(['category' => 'nvr']);
        Equipment::factory()->create(['category' => 'accessory', 'sku' => 'HDD-1TB']);

        $this->get(route('public.configurator'))->assertOk();
    }

    public function test_cable_calculator_page_loads(): void
    {
        $this->get(route('public.cable-calculator'))->assertOk();
    }

    public function test_contact_page_loads(): void
    {
        $this->get(route('public.contact'))->assertOk();
    }

    public function test_blog_index_loads(): void
    {
        Post::factory()->create(['published_at' => now()->subDay()]);

        $this->get(route('public.blog.index'))->assertOk();
    }

    public function test_blog_show_loads_for_published_post(): void
    {
        $post = Post::factory()->create(['published_at' => now()->subDay()]);

        $this->get(route('public.blog.show', $post->slug))->assertOk();
    }

    public function test_blog_show_returns_404_for_unpublished_post(): void
    {
        $post = Post::factory()->create(['published_at' => null]);

        $this->get(route('public.blog.show', $post->slug))->assertNotFound();
    }

    public function test_sitemap_and_robots_are_reachable(): void
    {
        $this->get(route('sitemap'))->assertOk();
        $this->get(route('robots'))->assertOk();
    }
}
