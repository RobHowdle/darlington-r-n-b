<?php

$rnbRailwayContentText = file_get_contents('content/rnb_railway_content.txt', FILE_IGNORE_NEW_LINES);
$locationAddress = file_get_contents('content/location_address.txt', FILE_IGNORE_NEW_LINES);

// This code takes everything in the given file, locates the words in the array and then adds a span tag around them to allow for specific styling on specific words
$aboutText = fopen('content/about.txt', 'r');
$wordArray = array("R'n'B", "Rhythm 'n' Blues","The Forum Music Centre");
$output = '';
while(!feof($aboutText)) {
    $line = fgets($aboutText);
    foreach($wordArray as $word) {
        if(strpos($line, $word) !== false) {
            $line = str_replace($word, "<span class='highlight'>".$word."</span>", $line);
        }
    }
    $output .= $line;
}

fclose($aboutText);

// Gig Archive Loop
$archiveDir = 'archive';
$dirs = [];

if(is_dir($archiveDir)) {
    $files = scandir($archiveDir);
    foreach($files as $file) {
        if ($file != '.' && $file != '..' && is_dir($archiveDir . '/' . $file)) {
            $dirs[] = $file;
        }
    }
}
