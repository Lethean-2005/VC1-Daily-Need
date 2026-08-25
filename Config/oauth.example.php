<?php
// Copy this file to Config/oauth.php and fill in real values.
// Config/oauth.php is git-ignored — never commit real credentials.
//
// Google: create an OAuth Client ID at https://console.cloud.google.com/apis/credentials
//   - Application type: Web application
//   - Authorized redirect URI: must exactly match 'redirect_uri' below

return [
    'google' => [
        'client_id'     => 'YOUR_GOOGLE_CLIENT_ID',
        'client_secret' => 'YOUR_GOOGLE_CLIENT_SECRET',
        'redirect_uri'  => 'http://localhost:8003/auth/google/callback',
    ],
];
