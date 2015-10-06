<?php
/*
 * AJAX endpoint to check if a site is using CloudFlare
 */

// Load all of net/http's classes. Composer would be overkill for such a small tool.
require 'includes/net-http/src/Net/Http.php';
require 'includes/net-http/src/Net/Http/Client.php';
require 'includes/net-http/src/Net/Http/Exception.php';
require 'includes/net-http/src/Net/Http/ProtocolError.php';
require 'includes/net-http/src/Net/Http/ClientError.php';
require 'includes/net-http/src/Net/Http/NetworkError.php';
require 'includes/net-http/src/Net/Http/Request.php';
require 'includes/net-http/src/Net/Http/Response.php';
require 'includes/net-http/src/Net/Http/ServerError.php';

function cleanURL($url) {
    // Remove http(s) at beginning (will be readded later)
    if (substr($url, 0, strlen('http://')) == 'http://') {
        $url = substr($url, strlen('http://'));
    } elseif (substr($url, 0, strlen('https://')) == 'https://') {
        $url = substr($url, strlen('https://'));
    }

    // Remove path after URL, if exists
    if (strpos($url, '/')) {
        $url = substr($url, 0, strpos($url, '/'));
    }

    // Re-add protocol to URL
    $url = 'http://' . $url; // no ssl is required for this check

    return $url;
}

function checkSite($url) {
    $checkURL = $url . '/cdn-cgi/trace';

    // Make request to confirm
    try {
        $request = new Net_Http_Client();
        $request->setVerifyPeer(false); // verifying ssl is not really required here
        $request->get($checkURL);

        if ($request->getStatus() != 200) {
            return false; // didn't return a successful response to debug cdn url - not on CloudFlare
        } elseif ($request->getHeader('Server') != 'cloudflare-nginx') {
            return false; // doesn't have the CloudFlare server field set
        }

        // All tests passed!
        return true;
    } catch (Exception $e) {
        // Some sort of problem occurred!
        die(json_encode(array(
            'status' => false,
            'message' => 'Couldn\'t connect to target!'
        )));
    }
}

// Response comes in JSON
header('Content-Type: application/json');

if (!isset($_GET['url'])) {
    // Missing URL argument
    die(json_encode(array(
        'status' => false,
        'message' => 'Invalid request'
    )));
}

$onCloudflare = !!checkSite( cleanURL($_GET['url']) );

echo json_encode(array(
    'status' => true,
    'on_cloudflare' => $onCloudflare
));





