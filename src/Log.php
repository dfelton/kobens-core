<?php

declare(strict_types=1);

namespace Kobens\Core;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

final class Log
{
    /**
     * @var Logger[]
     */
    private static $logger = [];

    public function getLogger(string $name): Logger
    {
        if (self::$logger[$name] ?? false) {
            self::$logger[$name] = new Logger($name);
            self::$logger[$name]->pushHandler(new StreamHandler(
                sprintf(
                    '%s/var/log/%s.log',
                    Config::getInstance()->getRootDir(),
                    $name
                ),
                Logger::WARNING
            ));
        }
        return self::$logger[$name];
    }
}
