<?php

class GoogleClientLite {
    private $clientId;
    private $clientSecret;
    private $redirectUri;
    private $tokenUrl = 'https://oauth2.googleapis.com/token';
    private $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth';
    private $userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo';
    private $scope = [
        'https://www.googleapis.com/auth/userinfo.email',
        'https://www.googleapis.com/auth/userinfo.profile'
    ];

    public function __construct($clientId, $clientSecret, $redirectUri) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
    }

    public function createAuthUrl() {

    $params = [
        'response_type' => 'code',
        'client_id' => $this->clientId,
        'redirect_uri' => $this->redirectUri,
        'scope' => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
        'access_type' => 'offline',
        'prompt' => 'consent'
    ];

    return $this->authUrl . '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
}

    public function fetchAccessTokenWithAuthCode($code) {
        $postData = [
            'code' => $code,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
            'grant_type' => 'authorization_code'
        ];
        $ch = curl_init($this->tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }

    public function getUserInfo($accessToken) {
        $ch = curl_init($this->userInfoUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $accessToken"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }
}
