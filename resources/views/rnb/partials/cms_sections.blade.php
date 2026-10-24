@foreach ($cmsPage->sections as $section)
    <div class="{{ ($historyLayout ?? false) ? 'history-block pb-2' : '' }}">
        @if ($section['type'] === 'hero')
            <h2 class="p">{{ $section['data']['heading'] }}</h2>
            @if (!empty($section['data']['text']))
                <p>{!! nl2br(e($section['data']['text'])) !!}</p>
            @endif
        @elseif ($section['type'] === 'rich_text')
            {!! \App\Support\RichText::clean($section['data']['body']) !!}
        @endif
    </div>
@endforeach
