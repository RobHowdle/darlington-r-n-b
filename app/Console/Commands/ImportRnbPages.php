<?php

namespace App\Console\Commands;

use App\Models\Page;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ImportRnbPages extends Command
{
    protected $signature = 'rnb:import-pages';

    protected $description = 'Import the original welcome and history once, preserving subsequent edits';

    public function handle(): int
    {
        $welcome = $this->blocks('homepage_text.txt');
        $history = $this->blocks('history_text.txt');
        if (count($welcome) !== 1 || count($history) > 50) {
            throw new RuntimeException('Unexpected source sections; import stopped.');
        }

        $definitions = [
            'welcome' => [
                'title' => $welcome[0]['heading'],
                'slug' => 'rnb-welcome',
                'sections' => [['type' => 'rich_text', 'data' => [
                    'body' => '<p>'.nl2br(e($welcome[0]['text'])).'</p>',
                ]]],
            ],
            'history' => [
                'title' => 'History Of The Club',
                'slug' => 'rnb-history',
                'sections' => array_map(fn (array $block): array => [
                    'type' => 'hero', 'data' => $block,
                ], $history),
            ],
        ];

        DB::transaction(function () use ($definitions): void {
            foreach ($definitions as $key => $definition) {
                if (Page::query()->where('site_key', $key)->exists()) {
                    $this->info("Preserved existing {$key} page.");
                    continue;
                }
                if (Page::query()->where('slug', $definition['slug'])->exists()) {
                    throw new RuntimeException('Import slug already exists: '.$definition['slug']);
                }
                $page = new Page([...$definition, 'status' => 'published']);
                $page->forceFill(['site_key' => $key])->save();
                $this->info("Imported {$key} page.");
            }
        });

        return self::SUCCESS;
    }

    private function blocks(string $file): array
    {
        $text = file_get_contents(resource_path('site-content/'.$file));
        if ($text === false) {
            throw new RuntimeException('Cannot read '.$file);
        }
        preg_match_all('~\[title\](.*?)\[/title\]\s*\[text\](.*?)\[/text\]~s', $text, $matches, PREG_SET_ORDER);
        if ($matches === [] || count($matches) !== substr_count($text, '[title]')
            || count($matches) !== substr_count($text, '[text]')) {
            throw new RuntimeException('Invalid source blocks in '.$file);
        }

        return array_map(function (array $match) use ($file): array {
            $heading = trim($match[1]);
            $body = trim($match[2]);
            if ($heading === '' || mb_strlen($heading) > 255 || mb_strlen($body) > 5000) {
                throw new RuntimeException('Source block exceeds editor limits in '.$file);
            }

            return ['heading' => $heading, 'text' => $body];
        }, $matches);
    }
}
