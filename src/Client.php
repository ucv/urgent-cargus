<?php
namespace UCV\UrgentCargus;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException as GuzzleClientException;
use UCV\UrgentCargus\Exception\ClientException as UrgentCargusClientException;

class Client
{
    /**
     * Library version
     */
    const VERSION = '0.1';

    /**
     * Default API Uri
     */
    const API_URI = 'https://urgentcargus.azure-api.net/api/';

    /**
     * @var string Subscription Key
     */
    private $apiKey;

    /**
     * @var string Api Uri
     */
    private $apiUri;

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * Set subscription key and uri for the UrgentCargus API
     *
     * @param string $apiKey
     * @param string $apiUri
     */
    public function __construct($apiKey, $apiUri = null)
    {
        if (!$apiKey) {
            throw new \InvalidArgumentException('The UrgentCargus API needs a subscription key.');
        }

        $this->apiKey = $apiKey;
        $this->apiUri = $apiUri ?: self::API_URI;

        $this->httpClient = new HttpClient([
            'base_uri' => $this->apiUri,
            'timeout' => 10,
            'allow_redirects' => false,
            'headers' => [
                'User-Agent' => 'UrgentCargusAPI-PHP (Version ' . self::VERSION . ')',
                'Content-Type' => 'application/json',
                'Accept-Charset' => 'utf-8',
            ]
        ]);
    }

    /**
     * Execute the request to the API
     *
     * @param string $method
     * @param string $endpoint
     * @param array $params
     * @param null|string $token
     * @return mixed
     */
    public function request($method, $endpoint, array $params = [], $token = null)
    {
        $headers = [
            'Ocp-Apim-Trace' => 'true',
            'Ocp-Apim-Subscription-Key' => $this->apiKey,
        ];
        if ($token) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        try {
            $response = $this->httpClient->request($method, $endpoint, [
                'headers' => $headers,
                'json' => $params,
            ]);
        } catch (GuzzleClientException $exception) {
            throw UrgentCargusClientException::fromException($exception);
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Shorthand for GET request
     *
     * @param string $endpoint
     * @param array $params
     * @param null|string $token
     * @return mixed
     */
    public function get($endpoint, array $params = [], $token = null)
    {
        return $this->request('GET', $endpoint, $params, $token);
    }

    /**
     * Shorthand for POST request
     *
     * @param string $endpoint
     * @param array $params
     * @param null|string $token
     * @return mixed
     */
    public function post($endpoint, array $params = [], $token = null)
    {
        return $this->request('POST', $endpoint, $params, $token);
    }

    /**
     * Shorthand for PUT request
     *
     * @param string $endpoint
     * @param array $params
     * @param null|string $token
     * @return mixed
     */
    public function put($endpoint, array $params = [], $token = null)
    {
        return $this->request('PUT', $endpoint, $params, $token);
    }

    /**
     * Shorthand for DELETE request
     *
     * @param string $endpoint
     * @param array $params
     * @param null|string $token
     * @return mixed
     */
    public function delete($endpoint, array $params = [], $token = null)
    {
        return $this->request('DELETE', $endpoint, $params, $token);
    }

    /**
     * Get token from service
     *
     * @param string $username
     * @param string $password
     * @return string
     */
    public function getToken($username, $password)
    {
        return $this->post('LoginUser', [
            'UserName' => $username,
            'Password' => $password
        ]);
    }
}
