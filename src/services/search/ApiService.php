<?php

namespace statikbe\udb\services\search;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use statikbe\udb\Environments;


class ApiService
{
    private string $clientId;
    private string $endpoint;
    private Client $client;

    public function __construct($clientId, Environments $environment)
    {
        $this->clientId = $clientId;
        $this->endpoint = $environment->getSearchEndpoint();

        $this->client = new Client([
            'base_uri' => $this->endpoint,
            'timeout' => 5.0,
            'connect_timeout' => 2.0,
        ]);

    }

    public function search($path, array $parameters): array
    {
        $url = $this->endpoint . $path;
        if ($parameters) {
            $url = $url . '?' . http_build_query($parameters);
        }

        $responseStatus = null;
        $tries = 0;

        try {
            while ($responseStatus !== 200 && $tries < 2) {
                $tries++;

                $headers = [
                    "X-Client-Id" => $this->clientId,
                ];

                $request = new Request(
                    'GET', $url, $headers
                );

                $response = $this->client->send($request);
                $returnData = json_decode(
                    utf8_encode($response->getBody()->getContents()),
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                );
                return $returnData;
            }
        } catch (\Throwable $e) {
            throw $e;
        }

        return $responseStatus;

    }

}
