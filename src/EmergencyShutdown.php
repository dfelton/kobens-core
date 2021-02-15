<?php

namespace Kobens\Core;

final class EmergencyShutdown implements EmergencyShutdownInterface
{
    private const FILENAME = 'emergency_shutdown';

    /**
     * @var string
     */
    private $dir;

    public function __construct(string $dir)
    {
        if (!\is_dir($dir)) {
            throw new \InvalidArgumentException("'$dir' is not a directory");
        } elseif (!\is_writeable($dir)) {
            throw new \Exception("'$dir' is not writtable");
        }
        $this->dir = $dir;
    }

    public function isShutdownModeEnabled(): bool
    {
        return \file_exists($this->getFilename());
    }

    public function enableShutdownMode(\Throwable $e): void
    {
        \touch($this->getFilename());
        $handle = \fopen($this->getFilename(), 'a');
        \flock($handle, LOCK_EX);
        \fwrite($handle, 'Shutdown Enabled at: '.(new \DateTime())->format('Y-m-d H:i:s').PHP_EOL);
        do {
            \fwrite($handle, 'Exception: ' . \get_class($e) . PHP_EOL);
            \fwrite($handle, 'Code: ' . $e->getCode() . PHP_EOL);
            \fwrite($handle, 'Message: ' . $e->getMessage() . PHP_EOL);
            \fwrite($handle, 'Strace:' . PHP_EOL.$e->getTraceAsString() . PHP_EOL);
            $e = $e->getPrevious();
            if ($e instanceof \Throwable) {
                \fwrite($handle, PHP_EOL . 'Previous Exception:' . PHP_EOL);
            }
        } while ($e instanceof \Throwable);
        \fwrite($handle, PHP_EOL.PHP_EOL);
        \flock($handle, LOCK_UN);
        \fclose($handle);
    }

    private function getFilename(): string
    {
        return $this->dir.'/'.self::FILENAME;
    }
}
