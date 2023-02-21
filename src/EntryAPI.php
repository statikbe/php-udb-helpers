<?php

namespace statikbe\udb;


use statikbe\udb\services\entry\ApiService;
use statikbe\udb\services\entry\AuthService;

class EntryAPI
{

    public ApiService $api;

    public function __construct(string $apiKey, string $storagePath, $environment = Environments::PROD)
    {
        // check if storage path exists & is writable, throw exception if not
        $this->api = new ApiService(
            $apiKey,
            $storagePath,
            $environment
        );

    }

    public function searchPlaces($params = [])
    {

        return $this->api->get('/places', $params);
    }

    public function getPlace($id)
    {
        return $this->api->get('/places/' . $id);
    }

    public function searchOrganizers($params = [])
    {

        return $this->api->get('/organizers', $params);
    }

    public function getOrganizer($id)

    {
        return $this->api->get('/organizers/' . $id);
    }
}