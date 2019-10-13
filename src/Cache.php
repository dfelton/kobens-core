<?php

namespace Kobens\Core;

use Zend\Cache\StorageFactory;
use Zend\Cache\Storage\StorageInterface;

final class Cache
{
    /**
     * @var StorageInterface
     */
    private static $instance;

    /**
     * @return StorageInterface
     */
    public static function getInstance(): StorageInterface
    {
        if (!self::$instance) {
            $dir = Config::getInstance()->getRootDir().'/var/cache';
            self::$instance = StorageFactory::factory([
                'adapter' => 'filesystem',
                'options' => ['cache_dir' => $dir]
            ]);
        }
        return self::$instance;
    }

}
