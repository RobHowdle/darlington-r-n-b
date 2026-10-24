<?php

namespace App\Console\Commands;

use App\Models\ClubSettings;
use Illuminate\Console\Command;
use RuntimeException;

class ImportRnbSettings extends Command
{
    protected $signature = 'rnb:import-settings';

    protected $description = 'Import original booking and venue details without overwriting edits';

    public function handle(): int
    {
        if (ClubSettings::query()->where('key', 'club')->exists()) {
            $this->info('Preserved existing club settings.');

            return self::SUCCESS;
        }
        $booking = $this->read('contact_text.txt');
        $venue = $this->read('location_text.txt');
        preg_match_all('~\[name\](.*?)\[/name\]\s*\[phone\](.*?)\[/phone\]\s*\[email\](.*?)\[/email\]~s',
            $this->read('contact_details.txt'), $matches, PREG_SET_ORDER);
        if ($matches === []) {
            throw new RuntimeException('No booking contacts found; import stopped.');
        }
        $data = [
            'booking_title' => $this->tag($booking, 'title'),
            'booking_text' => $this->tag($booking, 'text'),
            'contacts' => array_map(fn (array $row): array => [
                'name' => trim($row[1]), 'phone' => trim($row[2]), 'email' => trim($row[3]),
            ], $matches),
            'location_title' => $this->tag($venue, 'title'),
            'address' => $this->tag($venue, 'text'),
            'phone' => $this->tag($venue, 'phone'),
            // This legacy field contains the venue's contact-page URL, not an email address.
            'contact_url' => $this->tag($venue, 'email'),
            'facebook_url' => $this->tag($venue, 'facebook'),
            'instagram_url' => $this->tag($venue, 'instagram'),
        ];
        $request = new \App\Http\Requests\Admin\SaveClubSettingsRequest;
        validator($data, $request->rules())->validate();
        ClubSettings::query()->firstOrCreate(['key' => 'club'], ['data' => $data]);
        $this->info('Imported booking contacts and venue details.');

        return self::SUCCESS;
    }

    private function read(string $file): string
    {
        $text = file_get_contents(resource_path('site-content/'.$file));
        if ($text === false) {
            throw new RuntimeException('Cannot read '.$file);
        }

        return $text;
    }

    private function tag(string $source, string $tag): string
    {
        if (preg_match('~\['.$tag.'\](.*?)\[/'.$tag.'\]~s', $source, $match) !== 1) {
            throw new RuntimeException('Missing source field: '.$tag);
        }

        return trim($match[1]);
    }
}
