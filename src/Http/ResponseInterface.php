<?php

declare(strict_types=1);

namespace Kobens\Core\Http;

interface ResponseInterface extends \JsonSerializable
{
    public function getResponseCode(): int;

    public function getBody(): string;
}
