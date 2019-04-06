<?php

namespace Kobens\Core;

use Zend\Cache\StorageFactory;
use Kobens\Core\Exception\Exception;

final class Cache
{
    public function getCache()
    {
        /** @var \Zend\Config\Config $config */
        $config = (new Config())->cache;
        if ((string) $config->adapter->name === 'filesystem') {
            $this->initCacheDir($config->adapter->options->cache_dir);
        }

        return StorageFactory::factory($config);
    }

    private function initCacheDir(string $dir)
    {
        if (\is_dir($dir)) {
            if (!\is_readable($dir)) {
                throw new Exception("Cache Dir \"$dir\" is not readable.");
            }
            if (!\is_writable($dir)) {
                throw new Exception("Cache Dir \"$dir\" is not writeable.");
            }
        } elseif (!@\mkdir($dir, 0700)) {
            throw new Exception("Failed to make directory \"$dir\"");
        }
    }
}