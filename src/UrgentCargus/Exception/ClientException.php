<?php
namespace MNIB\UrgentCargus\Exception;

use GuzzleHttp\Exception\ClientException as GuzzleClientException;

class ClientException extends \RuntimeException
{
    public static function fromException(GuzzleClientException $exception)
    {
        $code = $exception->getResponse()->getStatusCode();
        $message = $exception->getMessage();

        if ($result = json_decode($exception->getResponse()->getBody()->getContents())) {
            switch (true) {
                case isset($result->message) && $result->message:
                    $message = $result->message;
                    break;
                case isset($result->Error) && $result->Error:
                    $message = $result->Error;
                    break;
            }
        }

        return new self($message, $code);
    }
}
