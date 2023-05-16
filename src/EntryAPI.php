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

    public function createPlace($data): array
    {
        return $this->api->post($data, '/places');
    }

    public function createOrganizer($data): array
    {
        return $this->api->post($data, '/organizers');
    }

    public function searchOrganizers($params = [])
    {

        return $this->api->get('/organizers', $params);
    }

    public function getOrganizer($id)

    {
        return $this->api->get('/organizers/' . $id);
    }

    public function createEvent($data): array
    {
        return $this->api->post($data, '/events');
    }

    public function updateEvent($id, $data): array
    {
        return $this->api->post($data, '/events/'.$id, "PUT");
    }

    public function updateWorkflowStatus($id, $data)
    {
        return $this->api->post($data, '/events/'.$id.'/workflow-status', "PUT");
    }

    public function updatePlaceWorkflowStatus($id, $data)
    {
        return $this->api->post($data, '/places/'.$id.'/workflow-status', "PUT");
    }

    public function createMediaObject($data)
    {
        return $this->api->postMultiPart($data, '/images');
    }
}