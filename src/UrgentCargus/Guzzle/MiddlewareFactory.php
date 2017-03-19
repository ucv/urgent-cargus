<?php
namespace MNIB\UrgentCargus\Guzzle;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class MiddlewareFactory
{
    public static function retry()
    {
        return Middleware::retry(self::retryDecider(), self::retryDelay());
    }

    private static function retryDecider()
    {
        return function (
            $retries,
            Request $request,
            Response $response = null,
            RequestException $exception = null
        ) {
            // Limit the number of retries to 3
            if ($retries >= 3) {
                return false;
            }

            $shouldRetry = false;

            // Retry connection exceptions
            if ($exception instanceof ConnectException) {
                $shouldRetry = true;
            }

            if ($response) {
                // Retry on server errors
                if ($response->getStatusCode() >= 500) {
                    $shouldRetry = true;
                } elseif ($response->getStatusCode() === 429) {
                    $shouldRetry = true;
                }
            }

            return $shouldRetry;
        };
    }

    private static function retryDelay()
    {
        return function ($retries) {
            return (int)200 * $retries;
        };
    }
}
