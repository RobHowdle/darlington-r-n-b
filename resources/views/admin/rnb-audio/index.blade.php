@extends('admin.rnb-audio.layout')

@section('content')
    <div class="toolbar">
        <p class="muted">{{ $tracks->count() }} {{ \Illuminate\Support\Str::plural('track', $tracks->count()) }}</p>
        <a class="button" href="{{ route('admin.rnb-audio.create') }}">Add track</a>
    </div>

    @forelse ($tracks as $track)
        <article class="card row">
            <img class="cover" src="{{ $track->cover_url }}" alt="{{ $track->cover_alt ?: $track->title }}">
            <div class="grow">
                <h2>{{ $track->title }}</h2>
                <p class="muted">{{ $track->artist }} · Order {{ $track->sort_order }}</p>
                <span class="pill {{ $track->is_published ? 'published' : '' }}">
                    {{ $track->is_published ? 'Published' : 'Hidden' }}
                </span>
            </div>
            <div class="actions">
                <a href="{{ route('admin.rnb-audio.edit', $track) }}">Edit</a>
                <form method="post" action="{{ route('admin.rnb-audio.destroy', $track) }}" onsubmit="return confirm('Delete this track and its uploaded files?')">
                    @csrf
                    @method('DELETE')
                    <button class="danger" type="submit">Delete</button>
                </form>
            </div>
        </article>
    @empty
        <div class="card"><p>No tracks yet. Add a track to populate the public Audio page.</p></div>
    @endforelse
@endsection
