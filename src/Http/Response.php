<?php

declare(strict_types=1);

namespace Kobens\Core\Http;

class Response implements ResponseInterface
{
    private string $body;

    private int $responseCode;

    public function __construct(
        string $body,
        int $responseCode
    ) {
        $this->body = $body;
        $this->responseCode = $responseCode;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getResponseCode(): int
    {
        return $this->responseCode;
    }

    public function jsonSerialize()
    {
        return [
            'body' => $this->body,
            'response_code' => $this->responseCode
        ];
    }
}
