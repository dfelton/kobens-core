<?php
namespace Kobens\Core;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Log
{
    /**
     * @var Logger[]
     */
    protected static $logger = [];

    public function getLogger($name) : Logger
    {
        if (!isset(static::$logger[$name])) {
            static::$logger[$name] = new Logger($name);
            static::$logger[$name]->pushHandler(new StreamHandler(
                (new Config())->getRoot().'/var/log/'.$name.'.log',
                Logger::WARNING
            ));
        }
        return static::$logger[$name];
    }
}

