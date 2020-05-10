<?php

declare(strict_types=1);

namespace Kobens\Core\Http;

use Kobens\Core\Exception\Http\CurlException;

interface CurlInterface
{
    /**
     * @throws CurlException
     * @param string $url
     * @param array $config
     * @return mixed
     */
    public function request(string $url, array $config = []): ResponseInterface;
}
