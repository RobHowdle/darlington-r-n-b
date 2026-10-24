<section class="home-grid-section">
    <div class="res--140">
        <div class="inner-wrapper--1680">
            <div class="grid-wrapper">
                <div class="left-col">
                    <h1 class="blue-ds pb-1">{{ $cmsPage->title }}</h1>
                    @include('rnb.partials.cms_sections')
                    <a href="/history" class="btn ticket ticket--left mt-1">
                        <img src="media/img/ticket.png">
                        <div class="overlay-text">
                            <span class="button-text">History</span>
                        </div>
                    </a>
                </div>
                <div class="center-col">
                    <table>
                        <thead>
                            <tr>
                                <td class="h1 blue-ds">Calendar</td>
                            </tr>
                        </thead>
                        <tbody class="eventlist">
                        </tbody>
                    </table>
                    <a target="_blank" href="https://theforumonline.co.uk/events/" class="btn ticket ticket--left mt-1">
                        <img src="media/img/ticket.png">
                        <div class="overlay-text">
                            <span class="button-text">Events</span>
                        </div>
                    </a>
                </div>
                <div class="right-col">
                    <div class="upper-block">
                        <h2 class="blue-ds h1 pb-1">{{ $contactData['title'] }}</h2>
                        <p>{!! nl2br(e($contactData['text'])) !!}</p>
                        <div class="contacts-wrapper mt-1">
                            @foreach ($contacts as $contact)
                                <div class="contact">
                                    <p class="h5">{{ $contact['name'] }}</p>
                                    @if (!empty($contact['phone']))
<a class="link" href="tel:{{ $contact['phone'] }}">
                                        <span class="fas fa-phone-alt"></span>
                                    </a>
@endif

                                    @if (!empty($contact['email']))
<a class="link" href="mailto:{{ $contact['email'] }}">
                                        <span class="fas fa-envelope"></span>
                                    </a>
@endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="lower-block">
                        <h3 class="blue-ds h1 pb-1">{{ $locationData['title'] }}</h3>
                        <p>{!! nl2br(e($locationData['text'])) !!}</p>

                        <div class="socials mt-1">
                            @if (!empty($locationData['phone']))
<a class="link" href="tel:{{ $locationData['phone'] }}">
                                <span class="fas fa-phone-alt"></span>
                            </a>
@endif
                            @if (!empty($locationData['contact_url']))
<a class="link" href="{{ $locationData['contact_url'] }}">
                                <span class="fas fa-envelope"></span>
                            </a>
@endif
                            @if (!empty($locationData['facebook']))
<a class="link" href="{{ $locationData['facebook'] }}">
                                <span class="fab fa-facebook-f"></span>
                            </a>
@endif
                            @if (!empty($locationData['instagram']))
<a class="link" href="{{ $locationData['instagram'] }}">
                                <span class="fab fa-instagram"></span>
                            </a>
@endif
                        </div>
                        <div class="map mt-2">
                            <div class="mapouter">
                                <div class="gmap_canvas">
                                    <iframe width="100%" id="gmap_canvas"
                                        src="{{ $mapUrl }}"
                                        frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
                                    <style>
                                        .mapouter {
                                            position: relative;
                                            text-align: right;
                                            width: 100%;
                                        }
                                    </style>
                                    <style>
                                        .gmap_canvas {
                                            overflow: hidden;
                                            background: none !important;
                                            width: 100%;
                                        }
                                    </style>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>