<?php

namespace Kobens\Core;

final class EmergencyShutdown implements EmergencyShutdownInterface
{
    private const FILENAME = 'emergency_shutdown';

    /**
     * @var ConfigInterface
     */
    private $config;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    public function isShutdownModeEnabled(): bool
    {
        return \file_exists($this->getFilename());
    }

    public function enableShutdownMode(string $message): void
    {
        $handle = \fopen($this->getFilename(), 'a');
        \fwrite($handle, $message);
        \fclose($handle);
    }

    private function getFilename(): string
    {
        return $this->config->getRootDir().'/var/'.self::FILENAME;
    }
}
