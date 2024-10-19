<?php
require '../vendor/autoload.php'; // Use dotenv for environment variables

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Fetch client ID, client secret, and redirect URI from .env
$clientId = $_ENV['SPOTIFY_CLIENT_ID'];
$clientSecret = $_ENV['SPOTIFY_CLIENT_SECRET'];
$tokenFilePath = '../tokens.json'; // Path to the JSON file storing tokens

// Fetch tokens from JSON file
function fetchTokensFromJson() {
    global $tokenFilePath;
    return file_exists($tokenFilePath) ? json_decode(file_get_contents($tokenFilePath), true) : [];
}

// Refresh Spotify access token
function refreshAccessToken($refreshToken) {
    global $clientId, $clientSecret;

    $auth = base64_encode("$clientId:$clientSecret");
    $options = [
        'http' => [
            'header' => "Authorization: Basic $auth\r\nContent-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query(['grant_type' => 'refresh_token', 'refresh_token' => $refreshToken]),
            'ignore_errors' => true,
        ]
    ];
    $response = file_get_contents('https://accounts.spotify.com/api/token', false, stream_context_create($options));
    return $response !== FALSE ? json_decode($response, true) : [];
}

// Save access token and its expiration time to JSON file
function saveAccessTokenToJson($accessToken, $expiresIn, $refreshToken) {
    global $tokenFilePath;
    
    $tokensToSave = [
        'access_token' => $accessToken,
        'refresh_token' => $refreshToken,
        'expires_in' => time() + $expiresIn,
    ];

    file_put_contents($tokenFilePath, json_encode($tokensToSave));
}

// Fetch currently playing or last played track
function fetchTrack($accessToken) {
    $context = stream_context_create([
        'http' => [
            'header' => "Authorization: Bearer $accessToken",
            'method' => 'GET',
            'ignore_errors' => true
        ]
    ]);

    // Attempt to get the currently playing track
    $response = file_get_contents('https://api.spotify.com/v1/me/player/currently-playing', false, $context);

    // Default: not playing
    $isPlaying = false;

    // If no currently playing track, get the last played track
    if ($response === FALSE || empty($response)) {
        $response = file_get_contents('https://api.spotify.com/v1/me/player/recently-played?limit=1', false, $context);
        $isPlaying = false;
    } else {
        $data = json_decode($response);
        $isPlaying = $data->is_playing ?? false;  // Check if a track is currently playing
    }

    if ($response === FALSE) {
        echo json_encode(['error' => 'Unable to fetch track information.']);
        return;
    }

    $data = json_decode($response);
    $track = $data->item ?? ($data->items[0]->track ?? null);

    if ($track) {
        $track->is_playing = $isPlaying;  // Add is_playing flag
        echo json_encode($track); // Return track data as JSON
    } else {
        echo json_encode(['error' => 'No track information available.']);
    }
}

// Fetch most played tracks
function fetchMostPlayedTracks($accessToken) {
    $context = stream_context_create([
        'http' => [
            'header' => "Authorization: Bearer $accessToken",
            'method' => 'GET',
            'ignore_errors' => true
        ]
    ]);

    $response = file_get_contents('https://api.spotify.com/v1/me/top/tracks?limit=5', false, $context);

    if ($response === FALSE) {
        echo json_encode(['error' => 'Unable to fetch most played tracks.']);
        return;
    }

    echo $response; // Return the most played tracks as JSON
}

// Main flow
$tokens = fetchTokensFromJson();
$accessToken = $tokens['access_token'] ?? null;
$refreshToken = $tokens['refresh_token'] ?? null;

// Always check the expiration time and refresh the token if needed
if (!$accessToken || ($tokens['expires_in'] ?? 0) < time()) {
    // Use the refresh token to get a new access token
    if ($refreshToken) {
        $tokenData = refreshAccessToken($refreshToken);
        if (!empty($tokenData['access_token'])) {
            // Save the new access token and continue
            saveAccessTokenToJson($tokenData['access_token'], $tokenData['expires_in'], $refreshToken);
            $accessToken = $tokenData['access_token'];
        } elseif (!empty($tokenData['refresh_token'])) {
            // If the refresh token was updated, save it too
            saveAccessTokenToJson($tokenData['access_token'], $tokenData['expires_in'], $tokenData['refresh_token']);
            $accessToken = $tokenData['access_token'];
            $refreshToken = $tokenData['refresh_token'];
        } else {
            // Failed to refresh tokens, need user to reauthenticate
            echo json_encode(['error' => 'Failed to refresh the access token. Please reauthenticate.']);
            exit;
        }
    } else {
        // No refresh token available, require reauthentication
        echo json_encode(['error' => 'Refresh token not available. Please authenticate again.']);
        exit;
    }
}

// Determine action
$action = $_GET['action'] ?? '';
if ($action === 'currently-playing') {
    fetchTrack($accessToken);
} elseif ($action === 'most-played') {
    fetchMostPlayedTracks($accessToken);
} else {
    echo json_encode(['error' => 'Invalid action.']);
}
?>
