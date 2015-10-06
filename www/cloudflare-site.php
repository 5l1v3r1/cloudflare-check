<?php
/*
 * AJAX endpoint to retrieve a random site using CloudFlare
 */

$sites = array();

$fileBody = file_get_contents('cloudflare-sites.txt');
$fileLines = explode("\n", $fileBody);

// Parse file lines
foreach ($fileLines as $line) {
    $line = trim($line); // trim any whitespace
    if (substr($line, 0, 1) == '#') {
        // Ignore comments
        continue;
    } elseif ($line == '') {
        // Ignore blank lines
        continue;
    }

    $sites[] = $line;
}

// Send plaintext header
header('Content-Type: text/plain');

// Pick random site
$site = $sites[rand(0, count($sites) - 1)];
echo $site;

