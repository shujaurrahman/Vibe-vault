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
                <a selected="" class="sc-fQpRED cxdHYg" href="/">Vibe Vault</a>
                <a class="sc-fQpRED Dhbnx" href="/playlist/">Playlists</a>
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
    </div>

    <div class="sc-dsLQwm jLnLFD">
        <div class="sc-iKTcqh jHpHUL">
            <div class="sc-gnpbhQ jsvwDU">
                <h2>ABOUT</h2>
                <p>A micro web showcasing the 5 most played songs, along with a carefully curated playlist by Shuja tailored to different moods.</p>
            </div>
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



    function fetchMostPlayedTracks() {
    $.ajax({
        url: './includes/spotifyHandler.php?action=most-played',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.error) {
                $('#most-played').html('<p>' + data.error + '</p>');
            } else {
                // Clear the existing items before populating
                $('#most-played').empty();

                // Display most played tracks
                data.items.forEach(function(track, index) {
                    const songNumber = (index + 1).toString().padStart(3, '0'); // Format number with leading zeros

                    // Extract album name and album image
                    const albumName = track.album.name; // Assuming track.album is the album object

                    const trackHtml = 
                       `<div class="sc-epPVmt NsFvF" >
                        <a href="${track.external_urls.spotify}"> <!-- Use Spotify URL for the song -->
                            <div class="sc-fpSrms bIOudy">
                                <div class="sc-hfvVTD wVBPe">
                                    <span class="sc-ifyrTC fIZweP">${songNumber}</span>
                                    <span class="sc-dENhDJ diKIEv">${track.name}, ${track.artists.map(artist => artist.name).join(', ')}</span>
                                </div>
                                <div class="sc-eEPDDI GTFKC">
                                    <span>${albumName}</span>
                                </div>
                            </div>
                        </a>
                             </div>`;
                    $('#most-played').append(trackHtml);
                });
            }
        },
        error: function() {
            $('#most-played').html('<p>Error fetching most played tracks.</p>');
        }
    });
}


    // Fetch currently playing track every 30 seconds
    setInterval(fetchCurrentlyPlaying, 30000);
    // Fetch initially on page load
    fetchCurrentlyPlaying();
    fetchMostPlayedTracks();
</script>

</body>
</html>
