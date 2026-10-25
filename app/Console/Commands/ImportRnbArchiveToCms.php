<?php

namespace App\Console\Commands;

use App\Models\ArchiveYear;
use Howdle\Cms\Models\BlogType;
use Howdle\Cms\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportRnbArchiveToCms extends Command
{
    protected $signature = 'rnb:import-archive-to-cms {--dry-run : Show how many records would be copied}';

    protected $description = 'Copy legacy gig archive years into Howdle CMS Posts without changing the legacy records';

    public function handle(): int
    {
        $years = ArchiveYear::query()->orderBy('year')->get();
        $existingType = BlogType::query()->where('slug', 'archive')->first();
        $existingIds = $existingType
            ? Post::query()->where('blog_type_id', $existingType->id)->pluck('slug')->all()
            : [];
        $pending = $years->filter(fn (ArchiveYear $year): bool => ! in_array((string) $year->year, $existingIds, true));

        if ($this->option('dry-run')) {
            $this->info($pending->count().' archive years would be copied; '.$years->count().' legacy rows remain untouched.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($years): void {
            $type = BlogType::query()->firstOrCreate(
                ['slug' => 'archive'],
                ['name' => 'Gig Archive', 'singular_name' => 'Archive Year'],
            );

            foreach ($years as $year) {
                $slug = (string) $year->year;
                if (Post::query()->where('blog_type_id', $type->id)->where('slug', $slug)->exists()) {
                    $this->line("Preserved existing CMS archive entry for {$slug}.");
                    continue;
                }

                $listItems = collect($year->bands ?? [])
                    ->map(fn (string $band): string => '<li>'.e($band).'</li>')
                    ->implode('');
                $body = $listItems === '' ? '' : '<p><strong>Bands:</strong></p><ul>'.$listItems.'</ul>';

                Post::query()->create([
                    'blog_type_id' => $type->id,
                    'title' => $slug,
                    'slug' => $slug,
                    'excerpt' => $year->description,
                    'body' => $body,
                    'status' => $year->is_published ? 'published' : 'draft',
                    'sort_order' => $year->year,
                ]);
                $this->line("Copied archive year {$slug}.");
            }
        });

        $this->info('Archive copy complete. Legacy archive rows were preserved.');

        return self::SUCCESS;
    }
}
