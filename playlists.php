<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vibe Vault</title>
    <link rel="stylesheet" href="./playlist.css">
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
                <a class="sc-fQpRED Dhbnx" href="index.php">Home</a>
                <a selected="" class="sc-fQpRED cxdHYg" href="#">Playlists</a>
            </nav>
        </div>
    </div>

    <div class="sc-dnaUSb iTmhCe">
        <div id="playlist-container" class="sc-kkmypM rOJqX">
            <!-- Playlists will be injected here via JavaScript -->
        </div>
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

<script>
 async function fetchPlaylists() {
    try {
        const response = await fetch('./includes/spotifyHandler.php?action=playlists');
        const data = await response.json();  // Fetch and parse the response

        console.log('API Response:', data);  // Log the full response to inspect

        // Access the playlists from the items array
        const playlists = data.items;  // Correctly access the playlists here

        // Ensure playlists is an array
        if (!Array.isArray(playlists)) {
            console.error('Expected playlists to be an array but got something else.');
            return;
        }

        // Filter the playlists based on the owner's display name
        const filteredPlaylists = playlists.filter(playlist => playlist.owner.display_name === "Shuja ur Rahman");

        const playlistContainer = document.getElementById('playlist-container');
        playlistContainer.innerHTML = ''; // Clear any existing content

        filteredPlaylists.forEach(playlist => {
            const playlistElement = `
                <a href="${playlist.external_urls.spotify}" target="_blank">
                    <div class="sc-dkjaqt hPnXCl">
                        <img src="${playlist.images[0]?.url}" alt="${playlist.name} Thumbnail" class="sc-jCbFiK iePaZb" />
                        <p class="sc-cBYhjr jasTiv">${playlist.name}</p>
                    </div>
                </a>
            `;
            playlistContainer.innerHTML += playlistElement;
        });
    } catch (error) {
        console.error('Error fetching playlists:', error);
    }
}

window.onload = fetchPlaylists;

</script>

</body>
</html>
