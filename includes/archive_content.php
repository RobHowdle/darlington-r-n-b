<div class="res--140">
    <div class="inner-wrapper--1680">
        <h1 class="blue-ds pb-1 text-center">Gig Archive</h1>
        <p class="text-center">Travel back through the years with our gig archive showcasing all the bands that have
            played at our events
            over the years.</p>
        <div class="tab-container mt-2">
            <div class="tablink-wrapper">
                <?php
                $data = file_get_contents('content/archive_data.txt');
                $sections = preg_split('/\[year\]/', $data, -1, PREG_SPLIT_NO_EMPTY);

                // Loop through each section (year)
                foreach ($sections as $section) {
                    // Extract year, description, and bands from the section
                    preg_match('/(\d{4})\[\/year\]/', $section, $matches);
                    $year = $matches[1];
                ?>
                    <div class="tab">
                        <button class="tablinks"
                            onclick="openTab(event, 'Tab<?php echo $year; ?>')"><?php echo $year; ?></button>
                    </div>
                <?php
                }
                ?>
            </div>
            <?php
            // Loop through each section (year)
            foreach ($sections as $section) {

                // Extract year, description, and bands from the section
                preg_match('/^(\d{4})\[\/year\].*?\[description\](.*?)\[\/description\].*?\[bands\](.*?)\[\/bands\]/s', $section, $matches);


                if (!empty($matches[1])) {
                    $year = $matches[1]; // Year extracted correctly
                } else {
                    echo "Error: Year not found in section.<br>";
                    continue; // Skip this section if year is invalid
                }

                $description = trim($matches[2] ?? ''); // Extract description content
                $bands = trim($matches[3] ?? ''); // Extract bands content
            ?>
                <div id="Tab<?php echo htmlspecialchars($year); ?>" class="tabcontent" style="display: none;">
                    <h3 class="h1"><?php echo htmlspecialchars($year); ?></h3>
                    <?php if (!empty($description)) : ?>
                        <p class="h6 my-2"><?php echo nl2br(htmlspecialchars($description)); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($bands)) : ?>
                        <p class="h2"><strong>Bands:</strong></p>
                        <ul>
                            <?php
                            // Split bands by comma and loop through each band
                            $bands_array = explode(',', $bands);
                            foreach ($bands_array as $band) {
                                echo '<li>' . htmlspecialchars(trim($band)) . '</li>';
                            }
                            ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php
            }
            ?>

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