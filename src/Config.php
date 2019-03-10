<?php

namespace Kobens\Core;

use Zend\Config\Config as ZendConfig;
use Zend\Config\Reader\Xml;

class Config
{
    /**
     * @var ZendConfig
     */
    protected static $config;

    public function __construct(
        $filename = null
    )
    {
        if (static::$config === null && $filename === null) {
            throw new \Exception(\sprintf(
                'First time instantiation of "%s" requires a filename to load from',
                __CLASS__,
                ZendConfig::class
            ));
        } elseif (static::$config !== null && $filename !== null) {
            throw new \Exception(\sprintf(
                '"%s" cannot be re-instantiated with new config.',
                __CLASS__
            ));
        } elseif (static::$config === null && $filename !== null) {
            static::$config = new ZendConfig((new Xml())->fromFile($filename));
        }
    }

    public function get(string $name) : ZendConfig
    {
        return static::$config->get($name);
    }

    public function __get(string $name)
    {
        return $this->get($name);
    }
}