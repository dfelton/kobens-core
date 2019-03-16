<?php

namespace Kobens\Core;

use Zend\Db\Adapter\Adapter;

class Db
{
    /**
     * @var Adapter
     */
    protected static $adapter;

    public function getAdapter() : Adapter
    {
        if (!static::$adapter) {
            static::$adapter = new Adapter((new Config())->database->toArray());
        }
        return static::$adapter;
    }
}