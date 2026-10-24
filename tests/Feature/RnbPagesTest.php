<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RnbPagesTest extends TestCase
{
    use RefreshDatabase;

    private function page(string $key, string $status = 'published'): Page
    {
        $page = new Page([
            'title' => 'Club '.$key,
            'slug' => 'rnb-'.$key,
            'status' => $status,
            'sections' => [['type' => 'rich_text', 'data' => ['body' => '<p>Saved club content</p>']]],
        ]);
        $page->forceFill(['site_key' => $key])->save();

        return $page;
    }

    public function test_fixed_routes_use_cms_content_in_the_original_layout(): void
    {
        $this->page('welcome');
        $this->page('history');
        $this->get('/')->assertOk()->assertViewIs('rnb.index')
            ->assertSee('Saved club content')->assertSee('home-grid-section');
        $this->get('/history')->assertOk()->assertViewIs('rnb.history')
            ->assertSee('Saved club content');
    }

    public function test_drafts_require_authenticated_preview(): void
    {
        $page = $this->page('history', 'draft');
        $this->get('/history')->assertNotFound();
        $this->get("/admin/pages/{$page->id}/preview")->assertRedirect(route('login'));
        $this->actingAs(User::factory()->unverified()->create())
            ->get("/admin/pages/{$page->id}/preview")->assertRedirect(route('verification.notice'));
        $this->actingAs(User::factory()->create())
            ->get("/admin/pages/{$page->id}/preview")->assertOk()
            ->assertViewIs('rnb.history')->assertSee('Saved club content');
    }

    public function test_editor_updates_public_content_and_slug_changes_keep_the_fixed_route(): void
    {
        $page = $this->page('history');
        $this->actingAs(User::factory()->create())->put("/admin/pages/{$page->id}", [
            'title' => 'Our history', 'slug' => 'updated-history', 'status' => 'published',
            'sections' => [['type' => 'rich_text', 'data' => [
                'body' => '<p onclick="evil()">Updated club story<script>evil()</script></p>',
            ]]],
        ])->assertRedirect(route('admin.pages.edit', $page));
        auth()->logout();
        $this->get('/history')->assertOk()->assertSee('Updated club story')->assertDontSee('evil()');
    }

    public function test_import_can_be_repeated_without_overwriting_edits(): void
    {
        $this->artisan('rnb:import-pages')->assertSuccessful();
        $this->assertDatabaseCount('pages', 2);
        Page::query()->where('site_key', 'welcome')->firstOrFail()->update(['title' => 'Edited welcome']);
        $this->artisan('rnb:import-pages')->assertSuccessful();
        $this->assertDatabaseCount('pages', 2);
        $this->assertSame('Edited welcome', Page::query()->where('site_key', 'welcome')->value('title'));
    }
}
