<?php

namespace Kobens\Core;

use Zend\Db\Adapter\Adapter;

final class Db
{
    /**
     * @var Adapter
     */
    private static $adapter;

    public static function getAdapter() : Adapter
    {
        if (!self::$adapter) {
            self::$adapter = new Adapter(Config::getInstance()->get('database')->toArray());
        }
        return self::$adapter;
    }
}