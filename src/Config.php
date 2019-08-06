<?php

namespace Kobens\Core;

use Zend\Config\Config as ZendConfig;
use Zend\Config\Reader\Xml;

/**
 * Class Config
 * @package Kobens\Core
 *
 * TODO: getLogDir and getRootDir are starting to feel like they don't belong here. Single Purpose Object.
 */
final class Config
{
    /**
     * @var Config
     */
    private static $instance;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var ZendConfig
     */
    private $config;

    private function __construct() { }

    /**
     * @return Config
     */
    public static function getInstance(): self
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     *
     * @param string $filename
     * @throws \LogicException
     * @throws \Exception
     * @throws \Zend\Config\Exception\RuntimeException
     */
    public function setConfig(string $filename): void
    {
        switch (true) {
            case $this->config !== null:
                throw new \LogicException('Config is already set.');
            case !\is_file($filename):
                throw new \Exception(\sprintf('"%s" is not a file.'));
            case !\is_readable($filename):
                throw new \Exception(\sprintf('"%s" is not readable.'));
        }
        $this->config = new ZendConfig((new Xml())->fromFile($filename));
    }

    /**
     * @param string $filename
     * @throws \LogicException
     * @throws \Exception
     */
    public function setRootDir(string $filename): void
    {
        switch (true) {
            case $this->rootDir !== null:
                throw new \LogicException('Root directory is already set.');
            case !\is_dir($filename):
                throw new \Exception(\sprintf('"%s" is not a directory.'));
            case !\is_readable($filename):
                throw new \Exception(\sprintf('"%s" is not readable.'));
        }
        $this->rootDir = $filename;
    }

    /**
     * @return string
     */
    public function getRootDir(): string
    {
        return $this->rootDir;
    }

    public function getLogDir(): string
    {
        return $this->rootDir.DIRECTORY_SEPARATOR.'var'.DIRECTORY_SEPARATOR.'log';
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get(string $name)
    {
        return $this->config->get($name);
    }

}
