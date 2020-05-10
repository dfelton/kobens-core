<?php

declare(strict_types=1);

namespace Kobens\Core\Http;

class Response implements ResponseInterface
{
    private string $body;

    private int $code;

    public function __construct(
        string $body,
        int $code
    ) {
        $this->body = $body;
        $this->code = $code;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getCode(): int
    {
        return $this->code;
    }
}
