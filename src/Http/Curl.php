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

    /**
     * @param string $url
     * @param array $config
     * @return ResponseInterface
     * @throws CurlException
     */
    public function request(string $url, array $config = []): ResponseInterface
    {
        $config[CURLOPT_RETURNTRANSFER] = true;
        $ch = \curl_init($url);

        foreach ($config as $option => $value) {
            \curl_setopt($ch, $option, $value);
        }

        $body = (string) \curl_exec($ch);
        $code = (int) \curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $curlErrNo = \curl_errno($ch);

        $data = [
            'url' => $url,
            'curlinfo_connect_time' => \curl_getinfo($ch, CURLINFO_CONNECT_TIME),
            'curlinfo_total_time' => \curl_getinfo($ch, CURLINFO_TOTAL_TIME),
        ];

        if ($curlErrNo !== CURLE_OK) {
            $data['curl_errno'] = $curlErrNo;
            $data['curl_error'] = \curl_error($ch);
        }

        \curl_close($ch);

        $json = (string) \json_encode($data);
        if ($json) {
            $this->logger->info($json);
        }

        if ($curlErrNo !== CURLE_OK) {
            throw new CurlException($data['curl_error'], $curlErrNo, $json ? new \Exception($json) : null);
        }

        return new Response($body, $code);
    }
}
