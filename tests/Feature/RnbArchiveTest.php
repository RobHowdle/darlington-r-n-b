<?php

namespace Tests\Feature;

use App\Models\ArchiveYear;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RnbArchiveTest extends TestCase
{
    use RefreshDatabase;

    private function payload(int $year = 2026): array
    {
        return ['year' => $year, 'description' => 'Club anniversary',
            'bands' => ['January: First Band', 'February: Second Band :: Sold Out'], 'is_published' => true];
    }

    public function test_public_archive_only_shows_published_non_removed_years_in_order(): void
    {
        ArchiveYear::create($this->payload(2026));
        ArchiveYear::create($this->payload(2025));
        ArchiveYear::create([...$this->payload(2024), 'is_published' => false]);
        ArchiveYear::create($this->payload(2023))->delete();
        $this->get('/archive')->assertOk()->assertViewIs('rnb.archive')
            ->assertSeeInOrder(['Tab2025', 'Tab2026'])
            ->assertDontSee('Tab2024')->assertDontSee('Tab2023')->assertSee('First Band');
    }

    public function test_admin_routes_require_verified_authentication(): void
    {
        $year = ArchiveYear::create($this->payload());
        foreach (['/admin/archive', '/admin/archive/create', "/admin/archive/{$year->id}/edit"] as $url) {
            $this->get($url)->assertRedirect(route('login'));
        }
        $this->post('/admin/archive', $this->payload(2027))->assertRedirect(route('login'));
        $this->put("/admin/archive/{$year->id}", $this->payload())->assertRedirect(route('login'));
        $this->delete("/admin/archive/{$year->id}")->assertRedirect(route('login'));
        $this->post("/admin/archive/{$year->id}/restore")->assertRedirect(route('login'));
        $this->actingAs(User::factory()->unverified()->create());
        $this->post('/admin/archive', $this->payload(2027))->assertRedirect(route('verification.notice'));
        $this->delete("/admin/archive/{$year->id}")->assertRedirect(route('verification.notice'));
    }

    public function test_editor_can_create_update_remove_and_restore_as_draft(): void
    {
        $this->actingAs(User::factory()->create());
        $this->get('/admin/archive/create')->assertOk();
        $this->post('/admin/archive', $this->payload())->assertRedirect();
        $year = ArchiveYear::sole();
        $this->get('/admin/archive')->assertOk();
        $this->get("/admin/archive/{$year->id}/edit")->assertOk();
        $this->put("/admin/archive/{$year->id}", [...$this->payload(), 'bands' => ['Changed gig']])->assertRedirect();
        $this->assertSame(['Changed gig'], $year->fresh()->bands);
        $this->delete("/admin/archive/{$year->id}")->assertRedirect();
        $this->assertSoftDeleted($year);
        $this->post("/admin/archive/{$year->id}/restore")->assertRedirect();
        $this->assertFalse($year->fresh()->is_published);
        $this->assertNull($year->fresh()->deleted_at);
    }

    public function test_duplicate_years_and_invalid_entries_are_rejected(): void
    {
        ArchiveYear::create($this->payload());
        $this->actingAs(User::factory()->create());
        $this->post('/admin/archive', $this->payload())->assertSessionHasErrors('year');
        $this->post('/admin/archive', [...$this->payload(1800), 'bands' => ['']])
            ->assertSessionHasErrors(['year', 'bands.0']);
    }

    public function test_archive_content_is_escaped_in_the_public_template(): void
    {
        ArchiveYear::create([...$this->payload(), 'bands' => ['<script>unsafe()</script>'], 'description' => '<img src=x onerror=unsafe()>']);
        $this->get('/archive')->assertOk()->assertDontSee('<script>unsafe()', false)
            ->assertDontSee('<img src=x', false)->assertSee('&lt;script&gt;', false);
    }

    public function test_import_is_repeatable_and_preserves_edits_and_removed_years(): void
    {
        $this->artisan('rnb:import-archive')->assertSuccessful();
        $count = ArchiveYear::count();
        $this->assertGreaterThan(20, $count);
        $year = ArchiveYear::query()->firstOrFail();
        $year->update(['bands' => ['Edited archive entry']]);
        $year->delete();
        $this->artisan('rnb:import-archive')->assertSuccessful();
        $this->assertSame($count, ArchiveYear::withTrashed()->count());
        $this->assertSame(['Edited archive entry'], ArchiveYear::withTrashed()->findOrFail($year->id)->bands);
        $this->assertSoftDeleted($year);
    }
}
