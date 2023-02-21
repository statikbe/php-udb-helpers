<?php

namespace statikbe\udb\services\entry;

use GuzzleHttp\Client;
use statikbe\udb\Environments;

class AuthService
{
    protected $apiKey;

    private $jwtUrl;

    private $storagePath;
    private Environments $environment;


    public function __construct($apiKey, $storagePath, Environments $environment)
    {
        $this->apiKey = $apiKey;
        $this->jwtUrl = $environment->getJWTUrl();

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
                return $credentials['accessToken'];
            }
        } catch (\Exception $e) {
            throw $e;
        }
        return null;
    }

    /** Use this function the first time to create your tokens.
     * And manually save them in /data/udb-tokens/credentials.json
     *
     * {
     *   "accessToken":"access_token_from_url",
     *   "refreshToken":"refresh_token_from_url",
     * }
     */
    public function generalJwtUrl(): string
    {
        return $this->jwtUrl . '/connect?apiKey=' . $this->apiKey . '&destination=oob';
    }

    public function refreshAccessToken(): void
    {
        try {
            $requestUrl = $this->jwtUrl . '/refresh?apiKey=' . $this->apiKey . '&refresh=' . $this->getRefreshToken();
            $client = new Client();
            $response = $client->get($requestUrl);
            $accessToken = $response->getBody()->getContents();
            $this->updateAccessToken($accessToken);
        } catch (\Exception $e) {
            dd($e);
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

    private function updateAccessToken($accessToken): void
    {
        try {
            if (file_exists($this->storagePath)) {
                $file = file_get_contents(
                    $this->storagePath
                );
                $credentials = json_decode($file, true);
                $credentials['accessToken'] = $accessToken;
                $fileContents = json_encode($credentials);
                file_put_contents(
                    $this->storagePath,
                    $fileContents
                );
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
}