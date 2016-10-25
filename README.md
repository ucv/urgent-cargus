# UrgentCargus PHP API
The API is RESTful JSON over HTTP using [GuzzleHttp](http://docs.guzzlephp.org/en/latest/) as a HTTP client.

# Usage Examples
    $client = new \MNIB\UrgentCargus\Client($apiKey);
    ...
    $params = [
        'UserName' => 'username',
        'Password' => 'password',
    ];
    $token = $client->post('LoginUser', $params);
    ...
    $result = $client->tget('PickupLocations', [], $token);