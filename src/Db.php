<?php

namespace Kobens\Core;

use Zend\Db\Adapter\Adapter;

final class Db
{
    /**
     * @var Adapter
     */
    private static $adapter;

    public function getAdapter() : Adapter
    {
        if (!static::$adapter) {
            self::$adapter = new Adapter((new Config())->database->toArray());
        }
        return self::$adapter;
    }
}