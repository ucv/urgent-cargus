<?php
namespace MNIB\UrgentCargus\Exception;

use GuzzleHttp\Exception\ClientException;

class HandleClientException extends \RuntimeException
{
    public function __construct(ClientException $exception)
    {
        $code = $exception->getResponse()->getStatusCode();
        $message = $exception->getMessage();

        if ($result = json_decode($exception->getResponse()->getBody())) {
            $message = isset($result->Error) && $result->Error ? $result->Error : $message;
        }

        parent::__construct($message, $code);
    }
}
