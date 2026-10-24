<div class="res--140">
    <div class="inner-wrapper--1680">
        <h1 class="blue-ds pb-1 text-center">Gig Archive</h1>
        <p class="text-center">Travel back through the years with our gig archive showcasing all the bands that have played at our events over the years.</p>
        <div class="tab-container mt-2">
            <div class="tablink-wrapper">
                @foreach ($archiveYears as $archiveYear)
                    <div class="tab">
                        <button class="tablinks" onclick="openTab(event, 'Tab{{ $archiveYear->year }}')">{{ $archiveYear->year }}</button>
                    </div>
                @endforeach
            </div>
            @foreach ($archiveYears as $archiveYear)
                <div id="Tab{{ $archiveYear->year }}" class="tabcontent" style="display: none;">
                    <h3 class="h1">{{ $archiveYear->year }}</h3>
                    @if (!empty($archiveYear->description))
                        <p class="h6 my-2">{!! nl2br(e($archiveYear->description)) !!}</p>
                    @endif
                    @if (!empty($archiveYear->bands))
                        <p class="h2"><strong>Bands:</strong></p>
                        <ul>
                            @foreach ($archiveYear->bands as $band)
                                <li>{{ $band }}</li>
                            @endforeach
                        </ul>
                    @endif
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