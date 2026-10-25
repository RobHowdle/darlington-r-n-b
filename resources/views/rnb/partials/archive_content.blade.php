<div class="res--140">
    <div class="inner-wrapper--1680">
        <h1 class="blue-ds pb-1 text-center">Gig Archive</h1>
        <p class="text-center">Travel back through the years with our gig archive showcasing all the bands that have played at our events over the years.</p>
        <div class="tab-container mt-2">
            <div class="tablink-wrapper">
                @foreach ($archivePosts as $archivePost)
                    <div class="tab">
                        <button class="tablinks" onclick="openTab(event, 'Tab{{ $archivePost->slug }}')">{{ $archivePost->title }}</button>
                    </div>
                @endforeach
            </div>
            @foreach ($archivePosts as $archivePost)
                <div id="Tab{{ $archivePost->slug }}" class="tabcontent" style="display: none;">
                    <h3 class="h1">{{ $archivePost->title }}</h3>
                    @if (!empty($archivePost->excerpt))
                        <p class="h6 my-2">{!! nl2br(e($archivePost->excerpt)) !!}</p>
                    @endif
                    {!! $archivePost->body !!}
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
    function openFirstTab() {
        var firstTabButton = document.querySelector('.tablinks');
        if (firstTabButton) {
            firstTabButton.click();
        }
    }

    window.addEventListener('load', openFirstTab);

    function openTab(evt, tabName) {
        var i, tabcontent, tablinks;

        // Hide all tab content
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
            tabcontent[i].classList.remove("active");
        }

        // Remove active class from all tab buttons
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].classList.remove("active");
        }

        // Safeguard: Only update if the element exists
        var tabElement = document.getElementById(tabName);
        if (tabElement) {
            tabElement.style.display = "block"; // Set display to block
            tabElement.classList.add("active"); // Add active class
            evt.currentTarget.classList.add("active"); // Highlight the clicked tab
        } else {
            console.error("Element with ID '" + tabName + "' not found.");
        }
    }
</script>