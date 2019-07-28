<?php
namespace Kobens\Core;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

final class Log
{
    /**
     * @var Logger[]
     */
    private static $logger = [];

    public function getLogger($name) : Logger
    {
        if (!isset(self::$logger[$name])) {
            self::$logger[$name] = new Logger($name);
            self::$logger[$name]->pushHandler(new StreamHandler(
                //(new Config())->getRoot().'/var/log/'.$name.'.log',
                Config::getInstance()->getRootDir().'/var/log/'.$name.'.log',
                Logger::WARNING
            ));
        }
        return self::$logger[$name];
    }
}

