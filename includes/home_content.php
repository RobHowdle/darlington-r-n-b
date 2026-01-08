<?php
// Read the contents of the text file
$welcomeContentFileContents = file_get_contents('content/homepage_text.txt');
$contactDetailsFileContents = file_get_contents('content/contact_text.txt');
$contactsFileContents = file_get_contents('content/contact_details.txt');
$locationFileContents = file_get_contents('content/location_text.txt');

// Define an array to store the extracted data
$welcomeData = [];
$contactData = [];
$contacts = [];
$location = [];

// Use regular expressions to extract data based on the tags
preg_match('/\[title\](.*?)\[\/title\]/s', $welcomeContentFileContents, $matches);
if (isset($matches[1])) {
    $welcomeData['title'] = $matches[1];
}

preg_match('/\[text\](.*?)\[\/text\]/s', $welcomeContentFileContents, $matches);
if (isset($matches[1])) {
    $welcomeData['text'] = nl2br($matches[1]);
}

preg_match('/\[title\](.*?)\[\/title\]/s', $contactDetailsFileContents, $matches);
if (isset($matches[1])) {
    $contactData['title'] = $matches[1];
}

preg_match('/\[text\](.*?)\[\/text\]/s', $contactDetailsFileContents, $matches);
if (isset($matches[1])) {
    $contactData['text'] = nl2br($matches[1]);
}

preg_match('/\[title\](.*?)\[\/title\]/s', $locationFileContents, $matches);
if (isset($matches[1])) {
    $locationData['title'] = $matches[1];
}

preg_match('/\[text\](.*?)\[\/text\]/s', $locationFileContents, $matches);
if (isset($matches[1])) {
    $locationData['text'] = nl2br($matches[1]);
}

preg_match('/\[phone\](.*?)\[\/phone\]/s', $locationFileContents, $matches);
if (isset($matches[1])) {
    $locationData['phone'] = $matches[1];
}

preg_match('/\[email\](.*?)\[\/email\]/s', $locationFileContents, $matches);
if (isset($matches[1])) {
    $locationData['email'] = $matches[1];
}

preg_match('/\[facebook\](.*?)\[\/facebook\]/s', $locationFileContents, $matches);
if (isset($matches[1])) {
    $locationData['facebook'] = $matches[1];
}

preg_match('/\[instagram\](.*?)\[\/instagram\]/s', $locationFileContents, $matches);
if (isset($matches[1])) {
    $locationData['instagram'] = $matches[1];
}


preg_match_all('/\[name\](.*?)\[\/name\]\s*\[phone\](.*?)\[\/phone\]\s*\[email\](.*?)\[\/email\]/s', $contactsFileContents, $matches, PREG_SET_ORDER);

// Iterate through the matches and populate the $contacts array
foreach ($matches as $match) {
    $contact = [
        'name' => $match[1],
        'phone' => $match[2],
        'email' => $match[3]
    ];
    $contacts[] = $contact;
}
?>

<section class="home-grid-section">
    <div class="res--140">
        <div class="inner-wrapper--1680">
            <div class="grid-wrapper">
                <div class="left-col">
                    <h1 class="blue-ds pb-1"><?php echo $welcomeData['title']; ?></h1>
                    <p><?php echo $welcomeData['text']; ?></p>
                    <a href="history.php" class="btn ticket ticket--left mt-1">
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
                        <h2 class="blue-ds h1 pb-1"><?php echo $contactData['title']; ?></h2>
                        <p><?php echo $contactData['text']; ?></p>
                        <div class="contacts-wrapper mt-1">
                            <?php foreach ($contacts as $contact) : ?>
                                <div class="contact">
                                    <p class="h5"><?php echo $contact['name'] ?></p>
                                    <a class="link" href=" tel:<?php echo $contact['phone']; ?>">
                                        <span class="fas fa-phone-alt"></span>
                                    </a>

                                    <a class="link" href="mailto:<?php echo $contact['email']; ?>">
                                        <span class="fas fa-envelope"></span>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="lower-block">
                        <h3 class="blue-ds h1 pb-1"><?php echo $locationData['title']; ?></h3>
                        <p><?php echo $locationData['text']; ?></p>

                        <div class="socials mt-1">
                            <a class="link" href="tel:<?php echo $locationData['phone']; ?>">
                                <span class="fas fa-phone-alt"></span>
                            </a>
                            <a class="link" href="mailto:<?php echo $locationData['email']; ?>">
                                <span class="fas fa-envelope"></span>
                            </a>
                            <a class="link" href="<?php echo $locationData['facebook']; ?>">
                                <span class="fab fa-facebook-f"></span>
                            </a>
                            <a class="link" href="<?php echo $locationData['instagram']; ?>">
                                <span class="fab fa-instagram"></span>
                            </a>
                        </div>
                        <div class="map mt-2">
                            <div class="mapouter">
                                <div class="gmap_canvas">
                                    <iframe width="100%" id="gmap_canvas"
                                        src="https://maps.google.com/maps?q=%20The%20Forum%20Music%20Centre%20Borough%20Road%20Darlington%20DL1%201SG&t=k&z=19&ie=UTF8&iwloc=&output=embed"
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