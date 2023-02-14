<?php

declare(strict_types=1);

namespace Kobens\Core;

use Zend\Cache\StorageFactory;
use Zend\Cache\Storage\StorageInterface;

final class Cache
{
    private static ?StorageInterface $instance = null;

    public static function getInstance(): StorageInterface
    {
        if (self::$instance === null) {
            $dir = Config::getInstance()->getRootDir().'/var/cache';
            self::$instance = StorageFactory::factory([
                'adapter' => 'filesystem',
                'options' => ['cache_dir' => $dir]
            ]);
        }
        return self::$instance;
    }

}
