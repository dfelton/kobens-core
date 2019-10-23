<?php

namespace Kobens\Core\Exception\Http;

class RequestTimeoutException extends \Exception
{
    public function __construct(string $message = null, \Exception $previous = null)
    {
        parent::__construct($message, 408, $previous);
    }
}
