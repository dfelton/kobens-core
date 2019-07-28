<?php

namespace Kobens\Core;

use Zend\Cache\Storage\StorageInterface;
use Zend\Cache\StorageFactory;
use Kobens\Core\Exception\Exception;

final class Cache
{
    /**
     * @return StorageInterface
     * @throws Exception
     */
    public function getCache(): StorageInterface
    {
        $cacheConfig = Config::getInstance()->get('cache');
        if ((string) $cacheConfig->adapter->name === 'filesystem') {
            $this->initCacheDir($cacheConfig->adapter->options->cache_dir);
        }

        return StorageFactory::factory($cacheConfig);
    }

    /**
     * @param string $dir
     * @throws Exception
     */
    private function initCacheDir(string $dir): void
    {
        if (\is_dir($dir)) {
            if (!\is_readable($dir)) {
                throw new Exception("Cache Dir \"$dir\" is not readable.");
            }
            if (!\is_writable($dir)) {
                throw new Exception("Cache Dir \"$dir\" is not writable.");
            }
        } elseif (!@\mkdir($dir, 0700)) {
            throw new Exception("Failed to make directory \"$dir\"");
        }
    }
}