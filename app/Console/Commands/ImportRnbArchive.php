<?php

namespace App\Console\Commands;

use App\Models\ArchiveYear;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ImportRnbArchive extends Command
{
    protected $signature = 'rnb:import-archive';

    protected $description = 'Import legacy archive years, preserving existing edits and removed years';

    public function handle(): int
    {
        $text = file_get_contents(resource_path('site-content/archive_data.txt'));
        if ($text === false) {
            throw new RuntimeException('Cannot read archive source.');
        }
        preg_match_all('~\[year\](\d{4})\[/year\]\s*\[description\](.*?)\[/description\]\s*\[bands\](.*?)\[/bands\]~s',
            $text, $matches, PREG_SET_ORDER);
        if ($matches === [] || count($matches) !== substr_count($text, '[year]')) {
            throw new RuntimeException('Invalid archive blocks; import stopped.');
        }
        $definitions = [];
        foreach ($matches as $match) {
            $year = (int) $match[1];
            if (isset($definitions[$year])) {
                throw new RuntimeException('Duplicate year in source: '.$year);
            }
            $bands = array_values(array_filter(array_map('trim', explode(',', $match[3])),
                fn (string $band): bool => $band !== ''));
            $definitions[$year] = [
                'year' => $year, 'description' => trim($match[2]),
                'bands' => $bands, 'is_published' => true,
            ];
        }
        DB::transaction(function () use ($definitions): void {
            foreach ($definitions as $year => $definition) {
                if (ArchiveYear::withTrashed()->where('year', $year)->exists()) {
                    $this->info("Preserved {$year}.");
                    continue;
                }
                $request = new \App\Http\Requests\Admin\SaveArchiveYearRequest;
                validator($definition, $request->rules())->validate();
                ArchiveYear::create($definition);
                $this->info("Imported {$year} (".count($definition['bands']).' entries).');
            }
        });

        return self::SUCCESS;
    }
}
