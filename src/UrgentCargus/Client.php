<?php
namespace MNIB\UrgentCargus;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\HandlerStack;
use MNIB\UrgentCargus\Guzzle\MiddlewareFactory;

class Client
{
    /**
     * Library version
     */
    const VERSION = '0.2';

    /**
     * Default API Uri
     */
    const API_URI = 'https://urgentcargus.azure-api.net/api/';

    /**
     * Subscription Key
     *
     * @var string
     */
    private $apiKey;

    /**
     * Api Uri
     *
     * @var string
     */
    private $apiUri;

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * Set subscription key and uri for the UrgentCargus API
     *
     * @param $apiKey
     * @param $apiUri
     */
    public function __construct($apiKey, $apiUri = null)
    {
        if (!$apiKey) {
            throw new \InvalidArgumentException('The UrgentCargus API needs a subscription key.');
        }

        $this->apiKey = $apiKey;
        $this->apiUri = $apiUri ?: self::API_URI;

        $handlerStack = HandlerStack::create();
        $handlerStack->push(MiddlewareFactory::retry());

        $this->httpClient = new HttpClient([
            'handler' => $handlerStack,
            'base_uri' => $this->apiUri,
            'timeout' => 10,
            'allow_redirects' => false,
            'headers' => [
                'User-Agent' => 'UrgentCargusAPI-PHP (Version ' . self::VERSION . ')',
                'Content-Type' => 'application/json',
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
            'Content-Type' => 'application/json',
            'Ocp-Apim-Trace' => 'true',
            'Ocp-Apim-Subscription-Key' => $this->apiKey,
        ];
        if ($token) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        $response = $this->httpClient->request($method, $endpoint, [
            'headers' => $headers,
            'json' => $params,
        ]);

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
}
