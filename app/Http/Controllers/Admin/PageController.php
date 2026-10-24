<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SavePageRequest;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function index(): Response
    {
        Gate::authorize('viewAny', Page::class);

        return Inertia::render('admin/pages/Index', [
            'pages' => Page::query()->orderBy('title')->get(['id', 'title', 'slug', 'status']),
        ]);
    }

    public function create(): Response
    {
        Gate::authorize('create', Page::class);

        return Inertia::render('admin/pages/Edit');
    }

    public function store(SavePageRequest $request): RedirectResponse
    {
        $page = Page::create($request->pageData());

        return to_route('admin.pages.edit', $page);
    }

    public function edit(Page $page): Response
    {
        Gate::authorize('view', $page);

        return Inertia::render('admin/pages/Edit', ['page' => $page]);
    }

    public function update(SavePageRequest $request, Page $page): RedirectResponse
    {
        $page->update($request->pageData());

        return to_route('admin.pages.edit', $page);
    }

    public function preview(Page $page): Response|\Illuminate\Contracts\View\View
    {
        Gate::authorize('view', $page);

        if (in_array($page->getAttribute('site_key'), ['welcome', 'history'], true)) {
            return app(\App\Http\Controllers\Public\RnbController::class)->render($page, true);
        }

        return Inertia::render('public/Page', ['page' => $page, 'preview' => true]);
    }
}
