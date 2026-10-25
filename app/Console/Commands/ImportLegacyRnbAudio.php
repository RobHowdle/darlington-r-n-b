<?php

namespace App\Console\Commands;

use App\Models\RnbAudioTrack;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportLegacyRnbAudio extends Command
{
    protected $signature = 'rnb:import-legacy-audio';

    protected $description = 'Copy the bundled Darlington RnB tracks and covers into managed storage';

    public function handle(): int
    {
        $tracks = [
            ['All My Life', 'The Nimmo Brothers', 'All My Life.mp3', 'All My Life.jpg'],
            ['Back For More', 'WT Feaster Band', 'Back for More.mp3', 'Back For More.jpg'],
            ['Beware', 'Kyla Brox Band', 'Beware.mp3', 'album.jpg'],
            ['Blues With A Feeling', 'Eddie Martin Band', 'Blues With A Feeling.mp3', 'Blues With A Feeling.jpg'],
            ['Boogie Man', 'The Hamsters', 'Boogie Man.mp3', 'Boogie Man.jpg'],
            ['Break It Down', 'Normal Beaker', 'Break it Down.mp3', 'Break It Down.jpg'],
            ['Bring It On Home To Me', 'Rivers', 'Bring It On Home To Me.mp3', 'album.jpg'],
            ['Divin Duck', "John O'Leary Band", 'Divin Duck.mp3', 'album.jpg'],
            ['No Time For Jive', 'Lovers Lament', 'No Time for Jive.mp3', 'album.jpg'],
            ['Resting In The Blues', "Nicky Moore's Blues Corporation", 'Resting In The Blues.mp3', 'Resting In The Blues.jpg'],
            ['Roxette', 'Dr. Feelgood', 'Roxette.mp3', 'Roxette.jpg'],
            ['Shake Your Hips', 'The Backscratchers', 'Shake Your Hips.mp3', 'album.jpg'],
            ['Stop Breakin Down', 'The Blue Bishops', 'Stop Breakin Down.mp3', 'album.jpg'],
            ['Sugar Man', 'Connie Lush & Blues Shouter', 'Sugar Man.mp3', 'album.jpg'],
            ['Sugar Rush', 'Ian Siegal', 'Sugar Rush.mp3', 'Sugar Rush.jpg'],
            ['Travellin Light', "Blues 'N' Trouble", 'Travellin Light.mp3', 'album.jpg'],
            ['You Gave Me The Blues', "The Mighty 45's", 'You Gave Me The Blues.mp3', 'You Gave Me The Blues.jpg'],
        ];

        $imported = 0;
        foreach ($tracks as $index => [$title, $artist, $audioFile, $coverFile]) {
            if (RnbAudioTrack::query()->where('title', $title)->where('artist', $artist)->exists()) {
                $this->line("Already imported: {$title}");
                continue;
            }

            $audioSource = public_path('media/audio/'.$audioFile);
            $coverSource = public_path('media/img/'.$coverFile);
            if (! is_file($audioSource) || ! is_file($coverSource)) {
                $this->warn("Skipped {$title}: legacy audio or cover file is missing.");
                continue;
            }

            $audioPath = 'rnb-audio/tracks/'.basename($audioFile);
            $coverPath = 'rnb-audio/covers/'.basename($coverFile);
            if (! $this->copyLegacyFile($audioSource, $audioPath)
                || ! $this->copyLegacyFile($coverSource, $coverPath)) {
                Storage::disk('public')->delete([$audioPath, $coverPath]);
                $this->warn("Skipped {$title}: could not copy its audio or cover file.");
                continue;
            }

            RnbAudioTrack::query()->create([
                'title' => $title,
                'artist' => $artist,
                'audio_path' => $audioPath,
                'cover_path' => $coverPath,
                'cover_alt' => "Album cover for {$title} by {$artist}",
                'sort_order' => $index + 1,
                'is_published' => true,
            ]);
            $imported++;
            $this->info("Imported: {$title}");
        }

        $this->info("Imported {$imported} track(s).");

        return self::SUCCESS;
    }

    private function copyLegacyFile(string $source, string $destination): bool
    {
        $stream = fopen($source, 'r');
        if ($stream === false) {
            return false;
        }

        try {
            return Storage::disk('public')->put($destination, $stream);
        } finally {
            fclose($stream);
        }
    }
}
