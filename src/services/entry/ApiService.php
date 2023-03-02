<?php

namespace statikbe\udb\services\entry;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use statikbe\udb\Environments;


class ApiService extends AuthService
{
    private string $accessToken;

    private string $endpoint;

    private Environments $environment;

    public function __construct($apiKey, $storagePath, Environments $environment)
    {
        parent::__construct($apiKey, $storagePath, $environment);
        $this->apiKey = $apiKey;
        $this->environment = $environment;
        $this->endpoint = $environment->getEndpoint();
        $this->accessToken = $this->getAccessToken();
    }


    public function post($data, $endPoint, $method = "POST", $headers = [], $baseUrl = null)
    {
        $client = new Client();
        $uri = $this->endpoint . $endPoint;
        $responseStatus = null;
        $tries = 0;
        $returnData = [];

        while ($responseStatus !== 200 && $tries < 2) {
            $tries++;
            $headers = array_merge($headers, [
                "Authorization" => "Bearer {$this->accessToken}",
                "X-Api-Key" => $this->apiKey,
            ]);
            try {
                $request = new Request(
                    $method, $uri, $headers, json_encode($data)
                );

                $response = $client->send($request);

                if ($response) {
                    $responseStatus = $response->getStatusCode();

                    if ($responseStatus == 204) {
                        break;
                    }
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
                throw $e;
            } catch (\Exception $e) {

                throw $e;
            }
        }


        return $returnData;
    }

    public function get($path, $parameters = [])
    {

        $url = $this->endpoint . $path;
        if ($parameters) {
            $url = $url . '?' . http_build_query($parameters);
        }

        $client = new Client();
        $responseStatus = null;
        $tries = 0;

        try {
            while ($responseStatus !== 200 && $tries < 2) {
                try {
                    $tries++;

                    $headers = [
                        "Authorization" => "Bearer {$this->accessToken}",
                        "X-Api-Key" => $this->apiKey,
                    ];

                    $request = new Request(
                        'GET', $url, $headers
                    );

                    $response = $client->send($request);
                    $returnData = json_decode(
                        utf8_encode($response->getBody()->getContents()),
                        true,
                        512,
                        JSON_THROW_ON_ERROR
                    );
                    return $returnData;

                } catch (ClientException $e) {
                    if ($e->getResponse()->getStatusCode() === 401 || $e->getResponse()->getStatusCode() === 403) {
                        $this->refreshAccessToken();
                        continue;
                    }
                }
            }
        } catch (\Throwable $e) {
            throw $e;
        }

        return $responseStatus;

    }

    // Function is used to send Files (images) to UIT
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

                $response = json_decode(utf8_encode($bodyResponse), true, 512, JSON_THROW_ON_ERROR);

                return $response;
            } catch (ClientException $e) {
                $tries++;
                if ($e->getResponse()->getStatusCode() === 401 || $e->getResponse()->getStatusCode() === 403) {
                    $this->refreshAccessToken();
                    continue;
                }

            } catch (\Throwable $e) {
                throw $e;
            }
        }

        return $responseStatus;
    }

}