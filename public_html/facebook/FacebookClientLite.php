<?php

class FacebookClientLite
{
    private $appId;
    private $appSecret;
    private $redirectUri;
    private $graphVersion = 'v18.0';
    private $logFile;

    public function __construct($appId, $appSecret, $redirectUri)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->redirectUri = $redirectUri;
        $this->logFile = __DIR__ . '/debug_facebook.log';
    }

    public function getLoginUrl($state)
    {
        $params = http_build_query([
            'client_id' => $this->appId,
            'redirect_uri' => $this->redirectUri,
            'state' => $state,
            'scope' => 'email'
        ]);

        return "https://www.facebook.com/{$this->graphVersion}/dialog/oauth?{$params}";
    }

    public function getAccessToken($code)
    {
        $url = "https://graph.facebook.com/{$this->graphVersion}/oauth/access_token?" . http_build_query([
            'client_id' => $this->appId,
            'redirect_uri' => $this->redirectUri,
            'client_secret' => $this->appSecret,
            'code' => $code
        ]);

        $response = $this->curlRequest($url);
        $data = json_decode($response, true);

        if (!$response || !isset($data['access_token'])) {
            $this->log('Access token alınamadı: ' . $response);
            return false;
        }


        return $data['access_token'];
    }

    public function getUser($accessToken)
    {
        $url = "https://graph.facebook.com/me?" . http_build_query([
            'fields' => 'id,name,email,picture.type(large)',
            'access_token' => $accessToken
        ]);

        $response = $this->curlRequest($url);
        $data = json_decode($response, true);

        if (!$response || !isset($data['id'])) {
            $this->log('User bilgisi alınamadı: ' . $response);
            return false;
        }

        return $data;
    }
    private function curlRequest($url)
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 15
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $this->log('cURL error: ' . curl_error($ch));
        }

        curl_close($ch);

        return $response;
    }

    private function log($message)
    {
        file_put_contents(
            $this->logFile,
            date('Y-m-d H:i:s') . " - " . $message . PHP_EOL,
            FILE_APPEND
        );
    }
}