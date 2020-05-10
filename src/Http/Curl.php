<?php

declare(strict_types=1);

namespace Kobens\Core\Http;

use Kobens\Core\Exception\Http\CurlException;
use Psr\Log\LoggerInterface;

final class Curl implements CurlInterface
{
    private LoggerInterface $logger;

    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    public function request(string $url, array $config = []): ResponseInterface
    {
        $ch = \curl_init($url);

        $config[CURLOPT_RETURNTRANSFER] = true;

        foreach ($config as $option => $value) {
            \curl_setopt($ch, $option, $value);
        }

        $body = (string) \curl_exec($ch);
        $code = (int) \curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

        $data = [
            'url' => $url,
            'curl_errno' => \curl_errno($ch),
            'curl_error' => \curl_error($ch),
            'curlinfo_connect_time' => \curl_getinfo($ch, CURLINFO_CONNECT_TIME),
            'curlinfo_total_time' => \curl_getinfo($ch, CURLINFO_TOTAL_TIME),
        ];

        \curl_close($ch);

        $json = (string) \json_encode($data);
        if ($json) {
            $this->logger->info($json);
        }

        if ($data['curl_errno'] !== CURLE_OK) {
            throw new CurlException($data['curl_error'], $data['curl_errno'], $json ? new \Exception($json) : null);
        }

        return new Response($body, $code);
    }
}
