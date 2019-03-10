<?php

namespace Kobens\Core;

use Zend\Cache\StorageFactory;

class Cache
{
    public function getCache()
    {
        return StorageFactory::factory((new Config())->cache);
    }
}