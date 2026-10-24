<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveArchiveYearRequest;
use App\Models\ArchiveYear;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ArchiveYearController extends Controller
{
    public function index(): Response
    {
        Gate::authorize('viewAny', Page::class);

        return Inertia::render('admin/archive/Index', [
            'years' => ArchiveYear::withTrashed()->orderByDesc('year')
                ->get(['id', 'year', 'is_published', 'deleted_at']),
        ]);
    }

    public function create(): Response
    {
        Gate::authorize('viewAny', Page::class);

        return Inertia::render('admin/archive/Edit');
    }

    public function store(SaveArchiveYearRequest $request): RedirectResponse
    {
        $year = ArchiveYear::create($request->validated());

        return to_route('admin.archive.edit', $year);
    }

    public function edit(ArchiveYear $archiveYear): Response
    {
        Gate::authorize('viewAny', Page::class);

        return Inertia::render('admin/archive/Edit', ['archiveYear' => $archiveYear]);
    }

    public function update(SaveArchiveYearRequest $request, ArchiveYear $archiveYear): RedirectResponse
    {
        $archiveYear->update($request->validated());

        return to_route('admin.archive.edit', $archiveYear);
    }

    public function destroy(ArchiveYear $archiveYear): RedirectResponse
    {
        Gate::authorize('viewAny', Page::class);
        $archiveYear->delete();

        return to_route('admin.archive.index');
    }

    public function restore(ArchiveYear $archiveYear): RedirectResponse
    {
        Gate::authorize('viewAny', Page::class);
        // Restored years return as drafts so publication remains an explicit choice.
        $archiveYear->is_published = false;
        $archiveYear->restore();
        $archiveYear->save();

        return to_route('admin.archive.edit', $archiveYear);
    }
}
