<?php

namespace statikbe\udb\services\authentication;

use GuzzleHttp\Client;
use statikbe\udb\Environments;

class AuthService
{
    protected string $apiKey;

    private string $jwtUrl;

    private string $storagePath;

    protected Environments $environment;

    public function __construct($apiKey, $storagePath, Environments $environment)
    {
        $this->apiKey = $apiKey;
        $this->environment = $environment;
        $this->jwtUrl = $this->environment->getJWTUrl();

        $this->storagePath = $storagePath;
    }

    /**
     * @throws \JsonException
     */
    public function getAccessToken(): string|null
    {
        if (file_exists($this->storagePath)) {
            $file = file_get_contents(
                $this->storagePath
            );
            $credentials = json_decode($file, true, 512, JSON_THROW_ON_ERROR);

            return $credentials['accessToken'];
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

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function refreshAccessToken(): void
    {
        $requestUrl = $this->jwtUrl . '/refresh?apiKey=' . $this->apiKey . '&refresh=' . $this->getRefreshToken();
        $client = new Client();
        $response = $client->get($requestUrl);
        $accessToken = $response->getBody()->getContents();
        $this->updateAccessToken($accessToken);
    }

    /**
     * @throws \JsonException
     */
    private function getRefreshToken(): string|null
    {
        if (file_exists($this->storagePath)) {
            $file = file_get_contents(
                $this->storagePath
            );
            $credentials = json_decode($file, true, 512, JSON_THROW_ON_ERROR);

            return $credentials['refreshToken'];
        }

        return null;
    }

    /**
     * @throws \JsonException
     */
    private function updateAccessToken($accessToken): void
    {
        if (file_exists($this->storagePath)) {
            $file = file_get_contents(
                $this->storagePath
            );
            $credentials = json_decode($file, true, 512, JSON_THROW_ON_ERROR);
            $credentials['accessToken'] = $accessToken;
            $fileContents = json_encode($credentials, JSON_THROW_ON_ERROR);
            file_put_contents(
                $this->storagePath,
                $fileContents
            );
        }
    }
}