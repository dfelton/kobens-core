<?php

declare(strict_types=1);

namespace Kobens\Core\Http;

interface ResponseInterface
{
    public function getCode(): int;

    public function getBody(): string;
}
