<?php

namespace statikbe\udb;


use statikbe\udb\services\search\ApiService;

class SearchAPI
{

    /**
     * @var ApiService
     */
    public ApiService $api;

    /**
     * @param string $apiKey
     * @param $environment
     */
    public function __construct(string $apiKey, $environment = Environments::PROD)
    {
        $this->api = new ApiService(
            $clientId = $apiKey,
            $environment,
        );
    }

    /**
     * @param array $params
     * @return array
     * @throws \JsonException
     * @throws \Throwable
     */
    public function searchEvents(array $params): array
    {
        return $this->api->search('/events', $params);
    }
}
