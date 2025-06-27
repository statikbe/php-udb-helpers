<?php

namespace statikbe\udb\services\entry;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use statikbe\udb\Environments;

class AuthService
{
    protected $apiKey;
    protected $clientId;
    protected $clientSecret;
    protected $authUrl;

    private $storagePath;
    private Environments $environment;


    public function __construct($clientId, $clientSecret, $storagePath, Environments $environment)
    {
        $this->authUrl = $environment->getOAuthUrl();
        $this->storagePath = $storagePath;
        $this->environment = $environment;
    }

    public function getAccessToken(): string|null
    {
        try {
            if (file_exists($this->storagePath)) {
                $file = file_get_contents(
                    $this->storagePath
                );
                $credentials = json_decode($file, true);
                if(!$credentials || !isset($credentials['access_token'])) {
                    $this->refreshAccessToken();
                    $file = file_get_contents($this->storagePath); // Re-read the updated token file
                    $credentials = json_decode($file, true);
                }
                return $credentials['access_token'];
            }
        } catch (\Exception $e) {
            throw $e;
        }
        return null;
    }

    public function refreshAccessToken(): void
    {
        try {
            $url = $this->authUrl . "/realms/uitid/protocol/openid-connect/token";
            $client = new Client();

            $request = new Request(
                "POST",
                $url,
                [
                    "Content-Type" => "application/x-www-form-urlencoded",
                ],
                "grant_type=client_credentials&client_id={$this->clientId}&client_secret={$this->clientSecret}");

            $response = $client->send($request);
            $body = $response->getBody()->getContents();
            $this->updateAccessToken($body);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function getRefreshToken(): string|null
    {
        try {
            if (file_exists($this->storagePath)) {
                $file = file_get_contents(
                    $this->storagePath
                );
                $credentials = json_decode($file, true);

                return $credentials['refreshToken'];
            }
        } catch (\Exception $e) {
           throw $e;
        }

        return null;
    }

    private function updateAccessToken($body): void
    {
        try {
            if (file_exists($this->storagePath)) {
                $file = file_get_contents(
                    $this->storagePath
                );
                file_put_contents(
                    $this->storagePath,
                    $body
                );
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
}