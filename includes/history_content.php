<?php
// Read the contents of the text file
$historyContentFileContents = file_get_contents('content/history_text.txt');

// Initialize arrays to store titles and texts
$titles = array();
$texts = array();

// Use regular expressions to extract data based on the tags
preg_match_all('/\[title\](.*?)\[\/title\]/s', $historyContentFileContents, $titleMatches);
if (!empty($titleMatches[1])) {
    $titles = $titleMatches[1];
}

preg_match_all('/\[text\](.*?)\[\/text\]/s', $historyContentFileContents, $textMatches);
if (!empty($textMatches[1])) {
    $texts = array_map('nl2br', $textMatches[1]);
}

?>

<section class="home-grid-section">
    <div class="res--140">
        <div class="inner-wrapper--1680">
            <div class="history-container">
                <h1 class="blue-ds pb-1">History Of The Club</h1>
                <?php
                for ($i = 0; $i < count($titles); $i++) { ?>
                <div class="history-block pb-2">
                    <h2 class="p"><?php echo $titles[$i]; ?></h2>
                    <p><?php echo $texts[$i]; ?></p>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</section>