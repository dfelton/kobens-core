<?php

namespace Kobens\Core;

use Zend\Config\Config as ZendConfig;
use Zend\Config\Reader\Xml;
use Kobens\Core\Exception\LogicException;

final class Config
{

    /**
     * @var ZendConfig
     */
    private static $config;

    /**
     * @var string
     */
    private static $root;

    public function __construct(string $filename = null, string $root = null)
    {
        if (self::$config === null && $filename === null) {
            throw new LogicException(\sprintf(
                'First time instantiation of "%s" requires a filename to load from',
                __CLASS__,
                ZendConfig::class
            ));
        } elseif (self::$config !== null && $filename !== null) {
            throw new LogicException(\sprintf(
                '"%s" cannot be re-instantiated with new config.',
                __CLASS__
            ));
        } elseif (self::$config === null && $filename !== null) {
            if ($root === null) {
                throw new LogicException(\sprintf(
                    'First time instantiation of "%s" requires specifying application root',
                    __CLASS__
                ));
            }
            self::$config = new ZendConfig((new Xml())->fromFile($filename));
            self::$root = $root;
        }
    }

    public function getRoot() : string
    {
        return self::$root;
    }

    public function get(string $name)
    {
        return self::$config->get($name);
    }

    public function __get(string $name)
    {
        return $this->get($name);
    }

}
