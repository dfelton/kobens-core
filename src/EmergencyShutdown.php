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

    public function enableShutdownMode(\Exception $e): void
    {
        \touch($this->getFilename());
        $handle = \fopen($this->getFilename(), 'a');
        \fwrite($handle, 'Shutdown Enabled at: '.(new \DateTime())->format('Y-m-d H:i:s').PHP_EOL);
        do {
            \fwrite($handle, 'Exception: '.\get_class($e).PHP_EOL);
            \fwrite($handle, 'Code: '.$e->getCode().PHP_EOL);
            \fwrite($handle, 'Message: '.$e->getMessage().PHP_EOL);
            \fwrite($handle, 'Strace:'.PHP_EOL.$e->getTraceAsString().PHP_EOL);
            $e = $e->getPrevious();
            if ($e instanceof \Exception) {
                \fwrite($handle, PHP_EOL.'Previous Exception:'.PHP_EOL);
            }
        } while ($e instanceof \Exception);
        \fwrite($handle, PHP_EOL.PHP_EOL);
        \fclose($handle);
    }

    private function getFilename(): string
    {
        return $this->config->getRootDir().'/var/'.self::FILENAME;
    }
}
