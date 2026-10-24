<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function show(Request $request): Response
    {
        abort_unless($request->isMethod('GET') || $request->isMethod('HEAD'), 404);

        $slug = $request->path();
        abort_if(str_contains($slug, '/'), 404);
        $page = Page::query()->where('slug', $slug)->where('status', 'published')->firstOrFail();

        return Inertia::render('public/Page', ['page' => $page, 'preview' => false]);
    }
}
