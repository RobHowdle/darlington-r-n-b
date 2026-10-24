<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Contracts\View\View;

class RnbController extends Controller
{
    public function home(): View
    {
        return $this->render($this->published('welcome'));
    }

    public function history(): View
    {
        return $this->render($this->published('history'));
    }

    public function archive(): View
    {
        return view('rnb.archive', [
            'archiveYears' => \App\Models\ArchiveYear::query()
                ->where('is_published', true)->orderBy('year')->get(),
        ]);
    }

    public function render(Page $page, bool $preview = false): View
    {
        $template = match ($page->getAttribute('site_key')) {
            'welcome' => 'rnb.index',
            'history' => 'rnb.history',
            default => abort(404),
        };

        $settings = \App\Models\ClubSettings::current();

        return view($template, [
            'cmsPage' => $page, 'preview' => $preview,
            'contactData' => ['title' => $settings['booking_title'], 'text' => $settings['booking_text']],
            'contacts' => $settings['contacts'],
            'locationData' => [
                'title' => $settings['location_title'], 'text' => $settings['address'],
                'phone' => $settings['phone'], 'contact_url' => $settings['contact_url'],
                'facebook' => $settings['facebook_url'], 'instagram' => $settings['instagram_url'],
            ],
            'mapUrl' => 'https://maps.google.com/maps?'.http_build_query([
                'q' => $settings['address'], 't' => 'k', 'z' => 19,
                'ie' => 'UTF8', 'iwloc' => '', 'output' => 'embed',
            ]),
        ]);
    }

    private function published(string $key): Page
    {
        return Page::query()->where('site_key', $key)
            ->where('status', 'published')->firstOrFail();
    }
}
