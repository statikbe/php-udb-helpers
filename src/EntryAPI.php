<?php

namespace statikbe\udb;

use statikbe\udb\services\entry\ApiService;
use statikbe\udb\services\entry\EntryService;

class EntryAPI
{
    public ApiService $api;

    /**
     * @throws \Exception
     */
    public function __construct(string $apiKey, string $storagePath, $environment = Environments::PROD)
    {
        // check if storage path exists & is writable, throw exception if not
        $this->api = new EntryService(
            $apiKey, $storagePath, $environment
        );
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function searchPlaces($params = [])
    {
        return $this->api->send('/places', $params);
    }

    /**
     * @throws \JsonException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPlace($id)
    {
        return $this->api->send('/places/' . $id);
    }

    /**
     * @throws \JsonException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function searchOrganizers($params = [])
    {
        return $this->api->send('/organizers', $params);
    }

    /**
     * @throws \JsonException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getOrganizer($id)
    {
        return $this->api->send('/organizers/' . $id);
    }
}