<?php
session_start();
require 'vendor/autoload.php'; // Use dotenv for environment variables

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Fetch client ID, client secret, and redirect URI from .env
$clientId = $_ENV['SPOTIFY_CLIENT_ID'];
$clientSecret = $_ENV['SPOTIFY_CLIENT_SECRET'];
$redirectUri = $_ENV['SPOTIFY_REDIRECT_URI'];

// Exchange the authorization code for access and refresh tokens
function exchangeAuthCodeForTokens($authCode) {
    global $clientId, $clientSecret, $redirectUri;
    $auth = base64_encode("$clientId:$clientSecret");
    $data = [
        'grant_type' => 'authorization_code',
        'code' => $authCode,
        'redirect_uri' => $redirectUri
    ];

    $options = [
        'http' => [
            'header' => "Authorization: Basic $auth\r\nContent-Type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data),
            'ignore_errors' => true,
        ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents('https://accounts.spotify.com/api/token', false, $context);

    if ($response === FALSE) return [];
    return json_decode($response, true);
}

// Save tokens to JSON file
function saveTokensToJson($tokenData) {
    global $tokenFilePath;
    $data = [
        'access_token' => $tokenData['access_token'],
        'refresh_token' => $tokenData['refresh_token'],
        'expires_in' => time() + $tokenData['expires_in']
    ];
    file_put_contents($tokenFilePath, json_encode($data));
}

if (isset($_GET['code'])) {
    // If the authorization code is returned, exchange it for tokens
    $authCode = $_GET['code'];
    $tokenData = exchangeAuthCodeForTokens($authCode);

    if (!empty($tokenData)) {
        saveTokensToJson($tokenData);
        // Redirect to index.php after successfully saving tokens
        header("Location: index.php?auth=success");
        exit();
    } else {
        echo "<p style='color: red;'>Failed to exchange authorization code for tokens. Please try again.</p>";
    }
} else {
    $authUrl = 'https://accounts.spotify.com/authorize?' . http_build_query([
        'client_id' => $clientId,
        'response_type' => 'code',
        'redirect_uri' => $redirectUri,
        'scope' => implode(' ', [
            'playlist-read-private',
            'playlist-read-collaborative',
            'user-library-read',
            'user-read-playback-state',
            'user-read-currently-playing',
            'user-read-recently-played',
            'user-top-read'
        ]),
    ]);

    header("Location: $authUrl");
    exit();
}
?>
