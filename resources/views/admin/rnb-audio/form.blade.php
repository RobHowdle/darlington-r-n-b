@extends('admin.rnb-audio.layout')

@section('content')
    @php($editing = $track->exists)
    <div class="card">
        <h2>{{ $editing ? 'Edit track' : 'Add track' }}</h2>
        <form method="post" enctype="multipart/form-data"
            action="{{ $editing ? route('admin.rnb-audio.update', $track) : route('admin.rnb-audio.store') }}">
            @csrf
            @if ($editing) @method('PUT') @endif

            <label for="title">Track title</label>
            <input id="title" name="title" type="text" maxlength="160" value="{{ old('title', $track->title) }}" required>
            @error('title')<span class="error">{{ $message }}</span>@enderror

            <label for="artist">Artist</label>
            <input id="artist" name="artist" type="text" maxlength="160" value="{{ old('artist', $track->artist) }}" required>
            @error('artist')<span class="error">{{ $message }}</span>@enderror

            <label for="audio">MP3 track {{ $editing ? '(leave blank to keep current file)' : '' }}</label>
            <input id="audio" name="audio" type="file" accept=".mp3,audio/mpeg" {{ $editing ? '' : 'required' }}>
            @if ($editing)<small><a href="{{ $track->audio_url }}" target="_blank" rel="noopener">Listen to current MP3</a></small>@endif
            @error('audio')<span class="error">{{ $message }}</span>@enderror

            <label for="cover">Album cover {{ $editing ? '(leave blank to keep current image)' : '' }}</label>
            <input id="cover" name="cover" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" {{ $editing ? '' : 'required' }}>
            @if ($editing)<p><img class="cover" src="{{ $track->cover_url }}" alt=""></p>@endif
            @error('cover')<span class="error">{{ $message }}</span>@enderror

            <label for="cover_alt">Cover image description</label>
            <input id="cover_alt" name="cover_alt" type="text" maxlength="200" value="{{ old('cover_alt', $track->cover_alt) }}">
            @error('cover_alt')<span class="error">{{ $message }}</span>@enderror

            <label for="sort_order">Display order</label>
            <input id="sort_order" name="sort_order" type="number" min="0" max="65535" value="{{ old('sort_order', $track->sort_order ?? 0) }}" required>
            @error('sort_order')<span class="error">{{ $message }}</span>@enderror

            <label><input name="is_published" type="checkbox" value="1" @checked(old('is_published', $track->is_published))>Show this track on the public Audio page</label>

            <div class="form-actions">
                <button type="submit">{{ $editing ? 'Save track' : 'Add track' }}</button>
                <a href="{{ route('admin.rnb-audio.index') }}">Cancel</a>
            </div>
        </form>
    </div>
@endsection
