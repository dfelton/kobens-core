<?php

namespace Kobens\Core;

use Zend\Config\Config as ZendConfig;
use Zend\Config\Reader\Xml;
use Kobens\Core\Exception\LogicException;

class Config
{
    /**
     * @var ZendConfig
     */
    protected static $config;

    /**
     * @var string
     */
    protected static $root;

    public function __construct(string $filename = null, string $root = null)
    {
        if (static::$config === null && $filename === null) {
            throw new LogicException(\sprintf(
                'First time instantiation of "%s" requires a filename to load from',
                __CLASS__,
                ZendConfig::class
            ));
        } elseif (static::$config !== null && $filename !== null) {
            throw new LogicException(\sprintf(
                '"%s" cannot be re-instantiated with new config.',
                __CLASS__
            ));
        } elseif (static::$config === null && $filename !== null) {
            if ($root === null) {
                throw new LogicException(\sprintf(
                    'First time instantiation of "%s" requires specifying application root',
                    __CLASS__
                ));
            }
            static::$config = new ZendConfig((new Xml())->fromFile($filename));
            static::$root = $root;
        }
    }

    public function getRoot() : string
    {
        return static::$root;
    }

    public function get(string $name) : ZendConfig
    {
        return static::$config->get($name);
    }

    public function __get(string $name) : ZendConfig
    {
        return $this->get($name);
    }
}