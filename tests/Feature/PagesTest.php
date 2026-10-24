<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PagesTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_replace([
            'title' => 'About us',
            'slug' => 'about-us',
            'status' => 'draft',
            'sections' => [
                ['type' => 'hero', 'data' => ['heading' => 'Our story', 'text' => 'Hello']],
                ['type' => 'rich_text', 'data' => ['body' => '<p>First <strong>chapter</strong>.</p>']],
            ],
        ], $overrides);
    }

    public function test_admin_pages_require_a_verified_user(): void
    {
        $page = Page::create($this->payload());
        foreach (['/admin/pages', '/admin/pages/create', "/admin/pages/{$page->id}/edit", "/admin/pages/{$page->id}/preview"] as $url) {
            $this->get($url)->assertRedirect(route('login'));
        }
        $this->post('/admin/pages', $this->payload())->assertRedirect(route('login'));
        $this->put("/admin/pages/{$page->id}", $this->payload())->assertRedirect(route('login'));
        $this->actingAs(User::factory()->unverified()->create());
        $this->get('/admin/pages')->assertRedirect(route('verification.notice'));
        $this->post('/admin/pages', $this->payload())->assertRedirect(route('verification.notice'));
    }

    public function test_editor_can_create_list_update_and_reorder_sections(): void
    {
        $this->actingAs(User::factory()->create());
        $this->get('/admin/pages/create')->assertOk()->assertInertia(fn (Assert $page) => $page->component('admin/pages/Edit'));
        $this->post('/admin/pages', $this->payload())->assertRedirect();
        $page = Page::sole();
        $this->assertSame(['hero', 'rich_text'], array_column($page->sections, 'type'));
        $this->get('/admin/pages')->assertOk()->assertInertia(fn (Assert $response) => $response
            ->component('admin/pages/Index')->has('pages', 1));
        $this->get("/admin/pages/{$page->id}/edit")->assertOk()->assertInertia(fn (Assert $response) => $response
            ->component('admin/pages/Edit')->where('page.slug', 'about-us'));

        $sections = array_reverse($page->sections);
        $this->put("/admin/pages/{$page->id}", $this->payload(['title' => 'Updated', 'sections' => $sections]))->assertRedirect();
        $this->assertSame('Updated', $page->fresh()->title);
        $this->assertSame(['rich_text', 'hero'], array_column($page->fresh()->sections, 'type'));
        $this->put("/admin/pages/{$page->id}", $this->payload(['sections' => [$sections[0]]]))->assertRedirect();
        $this->assertCount(1, $page->fresh()->sections);
    }

    public function test_formatted_content_survives_save_reopen_and_preview(): void
    {
        $this->actingAs(User::factory()->create());
        $page = Page::create($this->payload());
        $formatted = '<p><strong>Bold</strong> and <em>italic</em></p><ul><li><p>Bullet</p></li></ul><ol><li><p>Numbered</p></li></ol><p><a href="/contact">Contact</a></p>';

        $this->put("/admin/pages/{$page->id}", $this->payload(['sections' => [
            ['type' => 'rich_text', 'data' => ['body' => $formatted]],
        ]]))->assertRedirect(route('admin.pages.edit', $page));

        $saved = $page->fresh()->sections[0]['data']['body'];
        $this->assertStringContainsString('<strong>Bold</strong>', $saved);
        $this->assertStringContainsString('<em>italic</em>', $saved);
        $this->assertStringContainsString('<ul><li><p>Bullet</p></li></ul>', $saved);
        $this->assertStringContainsString('<ol><li><p>Numbered</p></li></ol>', $saved);
        $this->assertStringContainsString('href="/contact"', $saved);
        $this->get("/admin/pages/{$page->id}/edit")->assertOk()->assertInertia(fn (Assert $response) => $response
            ->component('admin/pages/Edit')->where('page.sections.0.data.body', $saved));
        $this->get("/admin/pages/{$page->id}/preview")->assertOk()->assertInertia(fn (Assert $response) => $response
            ->component('public/Page')->where('preview', true)->where('page.sections.0.data.body', $saved));
    }

    public function test_page_and_each_section_type_are_validated(): void
    {
        $this->actingAs(User::factory()->create());
        Page::create($this->payload());
        $this->post('/admin/pages', $this->payload(['slug' => 'about-us']))->assertSessionHasErrors('slug');
        foreach (['admin', 'login', 'settings', 'forgot-password', 'up', 'user', '_inertia'] as $slug) {
            $this->post('/admin/pages', $this->payload(['slug' => $slug]))->assertSessionHasErrors('slug');
        }
        $this->post('/admin/pages', $this->payload([
            'title' => '', 'slug' => 'Bad Slug', 'status' => 'live',
            'sections' => [
                ['type' => 'hero', 'data' => ['heading' => '', 'text' => 'Okay']],
                ['type' => 'rich_text', 'data' => ['body' => '']],
            ],
        ]))->assertSessionHasErrors(['title', 'slug', 'status', 'sections.0.data.heading', 'sections.1.data.body']);
        $this->post('/admin/pages', $this->payload(['sections' => [
            ['type' => 'video', 'data' => ['url' => 'https://example.com']],
        ]]))->assertSessionHasErrors('sections.0.type');
        $this->post('/admin/pages', $this->payload(['sections' => [
            ['type' => 'hero', 'data' => ['heading' => 'Okay', 'text' => '', 'onclick' => 'bad']],
        ]]))->assertSessionHasErrors('sections.0.data.onclick');
        $this->assertDatabaseCount('pages', 1);
    }

    public function test_draft_is_hidden_publicly_but_uses_public_renderer_in_authenticated_preview(): void
    {
        $page = Page::create($this->payload());
        $this->get('/about-us')->assertNotFound();
        $this->get("/admin/pages/{$page->id}/preview")->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create());
        $this->get("/admin/pages/{$page->id}/preview")->assertOk()->assertInertia(fn (Assert $response) => $response
            ->component('public/Page')->where('preview', true)->where('page.sections.0.data.heading', 'Our story'));
    }

    public function test_published_page_renders_and_rich_text_is_sanitized(): void
    {
        $this->actingAs(User::factory()->create());
        $this->post('/admin/pages', $this->payload([
            'status' => 'published',
            'sections' => [
                ['type' => 'hero', 'data' => ['heading' => 'A visible heading', 'text' => 'Visible introduction']],
                ['type' => 'rich_text', 'data' => ['body' => '<p onclick="alert(1)">Visible <strong>content</strong><script>alert(1)</script><a href="javascript:alert(1)">unsafe</a></p>']],
            ],
        ]))->assertRedirect();
        $page = Page::sole();
        $this->assertStringNotContainsString('onclick', $page->sections[1]['data']['body']);
        $this->assertStringNotContainsString('<script', $page->sections[1]['data']['body']);
        $this->assertStringNotContainsString('javascript:', $page->sections[1]['data']['body']);
        $this->assertStringContainsString('<strong>content</strong>', $page->sections[1]['data']['body']);
        auth()->logout();
        $this->get('/about-us')->assertOk()->assertInertia(fn (Assert $response) => $response
            ->component('public/Page')->where('preview', false)->where('page.title', 'About us'));

        $this->get('/login')->assertOk();
    }

    public function test_model_sanitizes_rich_text_from_any_writer(): void
    {
        $page = Page::create($this->payload(['sections' => [
            ['type' => 'rich_text', 'data' => ['body' => '<p onclick="run()">Safe<script>run()</script><a href="data:text/html,evil">link</a></p>']],
        ]]));
        $body = $page->fresh()->sections[0]['data']['body'];
        $this->assertSame('<p>Safe<a>link</a></p>', $body);
    }

    public function test_unpublishing_removes_public_access_and_route_conflicts_remain_protected(): void
    {
        $page = Page::create($this->payload(['status' => 'published']));
        $this->get('/about-us')->assertOk();
        $this->actingAs(User::factory()->create());
        $this->put("/admin/pages/{$page->id}", $this->payload())->assertRedirect();
        auth()->logout();
        $this->get('/about-us')->assertNotFound();
        $this->get('/admin')->assertRedirect(route('login'));
        $this->get('/settings/profile')->assertRedirect(route('login'));
        $this->get('/forgot-password')->assertOk();
        $this->get('/about-us/nested')->assertNotFound();
    }
}
