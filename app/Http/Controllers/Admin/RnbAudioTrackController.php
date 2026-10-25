<?php

namespace App\\Http\\Controllers\\Admin;

use App\\Http\\Controllers\\Controller;
use App\\Models\\RnbAudioTrack;
use Howdle\\Cms\\Models\\SiteSetting;
use Illuminate\\Http\\RedirectResponse;
use Illuminate\\Http\\Request;
use Illuminate\\Support\\Facades\\Gate;
use Illuminate\\Support\\Facades\\Storage;
use Illuminate\\Validation\\Rule;
use Illuminate\\View\\View;

class RnbAudioTrackController extends Controller
{
    public function index(): View
    {
        $this->authorizeCmsSettings();

        return view('admin.rnb-audio.index', [
            'tracks' => RnbAudioTrack::query()->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorizeCmsSettings();

        return view('admin.rnb-audio.form', ['track' => new RnbAudioTrack]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeCmsSettings();
        $data = $this->validated($request, true);
        $data['audio_path'] = $request->file('audio')->store('rnb-audio/tracks', 'public');
        $data['cover_path'] = $request->file('cover')->store('rnb-audio/covers', 'public');
        unset($data['audio'], $data['cover']);

        RnbAudioTrack::query()->create($data);

        return to_route('admin.rnb-audio.index')->with('status', 'Track added.');
    }

    public function edit(RnbAudioTrack $audioTrack): View
    {
        $this->authorizeCmsSettings();

        return view('admin.rnb-audio.form', ['track' => $audioTrack]);
    }

    public function update(Request $request, RnbAudioTrack $audioTrack): RedirectResponse
    {
        $this->authorizeCmsSettings();
        $data = $this->validated($request, false);

        if ($request->hasFile('audio')) {
            $oldAudio = $audioTrack->audio_path;
            $data['audio_path'] = $request->file('audio')->store('rnb-audio/tracks', 'public');
            Storage::disk('public')->delete($oldAudio);
        }

        if ($request->hasFile('cover')) {
            $oldCover = $audioTrack->cover_path;
            $data['cover_path'] = $request->file('cover')->store('rnb-audio/covers', 'public');
            Storage::disk('public')->delete($oldCover);
        }

        unset($data['audio'], $data['cover']);
        $audioTrack->update($data);

        return to_route('admin.rnb-audio.index')->with('status', 'Track updated.');
    }

    public function destroy(RnbAudioTrack $audioTrack): RedirectResponse
    {
        $this->authorizeCmsSettings();
        Storage::disk('public')->delete([$audioTrack->audio_path, $audioTrack->cover_path]);
        $audioTrack->delete();

        return to_route('admin.rnb-audio.index')->with('status', 'Track deleted.');
    }

    private function validated(Request $request, bool $creating): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:160'],
            'artist' => ['required', 'string', 'max:160'],
            'cover_alt' => ['nullable', 'string', 'max:200'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:65535'],
            'is_published' => ['nullable', 'boolean'],
            'audio' => [$creating ? 'required' : 'nullable', 'file', 'mimes:mp3', 'max:51200'],
            'cover' => [$creating ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ];

        $data = $request->validate($rules);
        $data['is_published'] = $request->boolean('is_published');

        return $data;
    }

    private function authorizeCmsSettings(): void
    {
        Gate::authorize('viewAny', SiteSetting::class);
    }
}
