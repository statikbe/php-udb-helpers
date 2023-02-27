<?php

namespace statikbe\udb\services\entry;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use statikbe\udb\Environments;
use statikbe\udb\services\authentication\AuthService;

class ApiService extends AuthService
{
    protected string $accessToken;

    protected string $endpoint;

    //private Environments $environment;

    /**
     * @throws \Exception
     */
    public function __construct($apiKey, $storagePath, Environments $environment)
    {
        parent::__construct($apiKey, $storagePath, $environment);
        $this->apiKey = $apiKey;
        $this->endpoint = $environment->getEndpoint();
        $this->accessToken = $this->getAccessToken();
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function sendJsonRequest($data, $endPoint, $method = "POST", $headers = [], $baseUrl = null)
    {
        $client = new Client();
        if ($baseUrl) {
            $uri = $baseUrl . $endPoint;
        } else {
            $uri = $this->environment->getEndpoint();
        }
        $responseStatus = null;
        $tries = 0;
        $returnData = [];
        $originalHeaders = $headers;

        while ($responseStatus !== 200 && $tries < 2) {
            // This part is needed to renew the request headers when auth has failed
            $headers = array_merge($originalHeaders, [
                "Authorization" => "Bearer {$this->accessToken}",
                "X-Api-Key" => $this->apiKey,
            ]);
            try {
                $request = new Request(
                    $method, $uri, $headers, json_encode($data, JSON_THROW_ON_ERROR)
                );

                $response = $client->send($request);

                $responseStatus = $response->getStatusCode();

                if ($responseStatus === 204) {
                    break;
                }

                return json_decode(
                    utf8_encode($response->getBody()->getContents()),
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                );
            } catch (ClientException $e) {
                if ($e->getResponse()->getStatusCode() === 401 || $e->getResponse()->getStatusCode() === 403) {
                    $this->refreshAccessToken();
                    continue;
                }
            }
        }

        return $returnData;
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function get($request)
    {
        $client = new Client();

        $response = $client->send($request);

        return json_decode(
            utf8_encode($response->getBody()->getContents()),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }

    // Function is used to send Files (images) to UIT

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     * @throws \JsonException
     */
    public function sendDataRequest($data, $endPoint)
    {
        $responseStatus = null;
        $tries = 0;
        $curlData = $data;

        while ($responseStatus !== 200 && $tries < 2) {
            try {
                $ch = curl_init();
                $curlData['file'] = curl_file_create($data['file'], 'image/jpeg');

                curl_setopt($ch, CURLOPT_URL, $this->udbUrl . $endPoint);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);

                $headers = [];
                $headers[] = 'Content-Type: multipart/form-data';
                $headers[] = 'Accept: application/json';
                $headers[] = 'Authorization: ' . 'Bearer ' . $this->accessToken;
                $headers[] = 'X-Api-Key: ' . $this->apiKey;
                curl_setopt($ch, CURLOPT_POSTFIELDS, $curlData);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $bodyResponse = curl_exec($ch);
                if (curl_errno($ch)) {
                    echo 'Error:' . curl_error($ch);
                }
                curl_close($ch);

                if (curl_error($ch)) {
                    $tries++;
                    $this->refreshAccessToken();
                    continue;
                }

                return json_decode(utf8_encode($bodyResponse), true, 512, JSON_THROW_ON_ERROR);
            } catch (ClientException $e) {
                $tries++;
                if ($e->getResponse()->getStatusCode() === 401 || $e->getResponse()->getStatusCode() === 403) {
                    $this->refreshAccessToken();
                    continue;
                }
            }
        }

        return $responseStatus;
    }
}