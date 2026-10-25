<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveClubSettingsRequest;
use App\Models\ClubSettings;
use Howdle\Cms\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ClubSettingsController extends Controller
{
    public function edit(): Response
    {
        Gate::authorize('viewAny', SiteSetting::class);

        return Inertia::render('admin/club/Edit', ['settings' => ClubSettings::current()]);
    }

    public function update(SaveClubSettingsRequest $request): RedirectResponse
    {
        $data = $request->validated();
        foreach ($data as $key => $value) {
            if ($key !== 'contacts') {
                $data[$key] = $value ?? '';
            }
        }
        $data['contacts'] = array_map(fn (array $contact): array => [
            'name' => $contact['name'], 'phone' => $contact['phone'] ?? '',
            'email' => $contact['email'] ?? '',
        ], $data['contacts']);
        ClubSettings::query()->updateOrCreate(['key' => 'club'], ['data' => $data]);

        return to_route('admin.club.edit');
    }
}
