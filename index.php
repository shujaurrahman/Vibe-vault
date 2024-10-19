<!DOCTYPE html>
<html>
<head>
    <meta charSet="utf-8"/>
    <meta name="viewport" content="width=device-width"/>
    <title>Vibe Vault</title>
    <link rel="stylesheet" href="./style-vibe.css">
</head>
<body>

<div class="sc-knesRu cMYPoz">
    <div class="sc-braxZu efPzzk">
        <div class="sc-gJhJTp dKguZo">
            <a href="/">
                <h1>
                    <svg width="12" height="13" viewBox="0 0 12 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="4" y="8.5" width="4" height="4" transform="rotate(-180 4 8.5)" fill="black"></rect>
                        <rect x="4" y="12.5" width="4" height="4" transform="rotate(-180 4 12.5)" fill="black"></rect>
                        <rect x="12" y="4.5" width="4" height="4" transform="rotate(-180 12 4.5)" fill="black"></rect>
                        <rect x="12" y="12.5" width="4" height="4" transform="rotate(-180 12 12.5)" fill="black"></rect>
                        <rect x="8" y="4.5" width="4" height="4" transform="rotate(-180 8 4.5)" fill="black"></rect>
                        <rect x="4" y="4.5" width="4" height="4" transform="rotate(-180 4 4.5)" fill="black"></rect>
                        <rect x="8" y="8.5" width="4" height="4" transform="rotate(-180 8 8.5)" fill="black"></rect>
                    </svg>
                    Vibe Vault
                </h1>
            </a>
            <nav class="sc-elDIKY juvebG">
                <a selected="" class="sc-fQpRED cxdHYg" href="#">Home</a>
                <a class="sc-fQpRED Dhbnx" href="playlists.php">Playlists</a>
            </nav>
        </div>
    </div>

    <div class="sc-dnaUSb iTmhCe">
        <div class="sc-gutikT lbtBoi">
            <div class="sc-huvEkS eWqmiJ">
                <span class="sc-fLVwEd gHbwDC">
                    <p>Currently Playing</p>
                </span>
                <span class="sc-gLXSEc hbVqhs" id="current-song">
                    <p>Loading...</p>
                </span>
            </div>
            <span class="sc-bSlUec jFKnhN" id="current-album">
                <p>Loading...</p>
            </span>
        </div>

        <div class="sc-eJgwjL bBOlEG " id="most-played">
        </div>
        <a selected="" class="sc-fQpRED cxdHYg" href="#">Load more</a>
    </div>

    <div class="sc-dsLQwm jLnLFD">
        <div class="sc-iKTcqh jHpHUL">
            <p class="sc-la-DxNn huYjqW">
                Say hey @
                <a href="https://shujaurrahman.me" target="_blank" class="sc-iCZwEW fxhTjv">shujaurrahman.me</a>
            </p>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function fetchCurrentlyPlaying() {
    $.ajax({
        url: './includes/spotifyHandler.php?action=currently-playing',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.error) {
                $('#current-song').html('<p>' + data.error + '</p>');
                $('#current-album').html('<p></p>');
            } else {
                if (data.is_playing) {
                    // If a track is currently playing, update label and display info
                    $('.sc-fLVwEd.gHbwDC p').text('Currently Playing');
                } else {
                    // If no track is playing, show last played track and update label
                    $('.sc-fLVwEd.gHbwDC p').text('Last Played');
                }
                
                // Display the track info
                $('#current-song').html('<p>' + data.name + ' by ' + data.artists[0].name + '</p>');
                
                // Display album info
                $('#current-album').html('<p>' + data.album.name + '</p>');
            }
        },
        error: function() {
            $('#current-song').html('<p>Error fetching currently playing track.</p>');
            $('#current-album').html('<p></p>');
        }
    });
}
let songCount = 10;  // Initially load 10 songs
let offset = 0;      // Initialize offset

function fetchMostPlayedTracks() {
    $.ajax({
        url: `./includes/spotifyHandler.php?action=most-played&limit=${songCount}&offset=${offset}`,
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.error) {
                $('#most-played').html('<p>' + data.error + '</p>');
            } else {
                // Clear the existing items before populating on first load
                if (songCount === 10) {
                    $('#most-played').empty();
                }

                // Display most played tracks
                data.items.forEach(function(track, index) {
                    const songNumber = (offset + index + 1).toString().padStart(3, '0'); // Format number with leading zeros

                    // Shorten the song name, artist name, and album name to 3 words
                    let shortenedName = track.name.split(' ').slice(0, 3).join(' ');
                    if (track.name.split(' ').length > 3) {
                        shortenedName += '..';
                    }

                    let artistName = track.artists[0].name.split(' ').slice(0, 3).join(' ');
                    if (track.artists[0].name.split(' ').length > 3) {
                        artistName += '..';
                    }

                    let albumName = track.album.name.split(' ').slice(0, 3).join(' ');
                    if (track.album.name.split(' ').length > 3) {
                        albumName += '..';
                    }

                    // Create the track HTML
                    const trackHtml = `
                    <div class="sc-epPVmt NsFvF">
                        <a href="${track.external_urls.spotify}">
                            <div class="sc-fpSrms bIOudy">
                                <div class="sc-hfvVTD wVBPe">
                                    <span class="sc-ifyrTC fIZweP">${songNumber}</span>
                                    <span class="sc-dENhDJ diKIEv">${shortenedName}</span>
                                </div>
                                <div class="sc-eEPDDI GTFKC">
                                    <span>${artistName}</span>
                                </div>
                            </div>
                        </a>
                    </div>`;

                    $('#most-played').append(trackHtml);
                });

                // Update offset for the next batch
                offset += data.items.length; // Update offset based on number of items fetched
            }
        },
        error: function() {
            $('#most-played').html('<p>Error fetching most played tracks.</p>');
        }
    });
}

// Function to load more tracks when "Load more" is clicked
function loadMoreTracks() {
    fetchMostPlayedTracks();  // Fetch more tracks
}

// Fetch currently playing track every 30 seconds
setInterval(fetchCurrentlyPlaying, 30000);

// Fetch initially on page load
fetchCurrentlyPlaying();
fetchMostPlayedTracks();

// Add event listener to "Load more" button
$(document).on('click', '.sc-fQpRED.cxdHYg', function(e) {
    e.preventDefault();
    loadMoreTracks();
});

</script>

</body>
</html>
