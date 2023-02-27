<?php

namespace statikbe\udb\services\entry;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use statikbe\udb\exceptions\MaxTriesException;

class EntryService extends ApiService
{
    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function send($path, $data = null, $headers = [], $parameters = [])
    {
        $url = $this->endpoint . $path;
        if ($parameters) {
            $url .= '?' . http_build_query($parameters);
        }

        if ($data) {
            $data = json_encode($data, JSON_THROW_ON_ERROR);
        }

        $headers = array_merge($headers, [
            "Authorization" => "Bearer {$this->accessToken}",
            "X-Api-Key" => $this->apiKey,
        ]);

        $request = new Request(
            'GET', $url, $headers, $data
        );

        return $this->get($request);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     * @throws \statikbe\udb\exceptions\MaxTriesException
     */
    public function sendAndRetry($path, $data = null, $maxTries = 3, $headers = [], $parameters = [])
    {
        $url = $this->endpoint . $path;
        if ($parameters) {
            $url .= '?' . http_build_query($parameters);
        }

        if ($data) {
            $data = json_encode($data, JSON_THROW_ON_ERROR);
        }
        $responseStatus = null;
        $tries = 0;
        while ($responseStatus !== 200 && $tries < $maxTries) {
            try {
                $newAuthHeader = [
                    "Authorization" => "Bearer {$this->accessToken}",
                    "X-Api-Key" => $this->apiKey,
                ];
                $headers = array_merge($headers, ...$newAuthHeader);

                $request = new Request(
                    'GET', $url, $headers, $data
                );

                $response = $this->get($request);
                $responseStatus = $response->getStatusCode();

                if ($responseStatus === 204) {
                    break;
                }

                return $response;
            } catch (ClientException $e) {
                if ($e->getResponse()->getStatusCode() === 401 || $e->getResponse()->getStatusCode() === 403) {
                    $this->refreshAccessToken();
                    continue;
                }
            }
        }
        throw new MaxTriesException();

    }
}