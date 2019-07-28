<?php

namespace Kobens\Core;

use Zend\Config\Config as ZendConfig;
use Zend\Config\Reader\Xml;
use Kobens\Core\Exception\LogicException;

/**
 * Class Config
 * @package Kobens\Core
 */
final class Config
{
    /**
     * @var bool
     */
    private $initialized;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var ZendConfig
     */
    private $config;

    private function __construct()
    {
        $this->initialized = false;
    }

    /**
     * @return Config
     */
    public static function getInstance(): self
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new self();
        }
        return $instance;
    }

    /**
     * @param string $filename
     * @param string $rootDir
     * @throws LogicException
     */
    public function initialize(string $filename, string $rootDir): void
    {
        if ($this->initialized) {
            throw new LogicException(sprintf('The instance of "%s" has already been initialized.', __CLASS__));
        }

        $this->rootDir = $rootDir;
        $this->config  = new ZendConfig((new Xml())->fromFile($filename));

        $this->initialized = true;
    }

    /**
     * @return bool
     */
    public function isInitialized(): bool
    {
        return $this->initialized;
    }

    /**
     * @return string
     */
    public function getRootDir(): string
    {
        return $this->rootDir;
    }

    // OLD METHODS
    /**
     * @return string
     * @deprecated
     */
    public function getRoot(): string
    {
        trigger_error(sprintf(
            'The method "%s" has been deprecated, use %s::getRootDir() instead.', __METHOD__, __CLASS__
        ));
        return $this->rootDir;
    }

    public function getLogDir(): string
    {
        return $this->rootDir.DIRECTORY_SEPARATOR.'var'.DIRECTORY_SEPARATOR.'log';
    }

    /**
     * Still in use, but this has been slightly changed to use non-static properties
     *
     * @param string $name
     * @return mixed
     */
    public function get(string $name)
    {
        return $this->config->get($name);
    }

    /**
     * @param string $name
     * @return mixed
     * @deprecated
     */
    public function __get(string $name)
    {
        trigger_error(sprintf('The method "%s" has been deprecated, use explicit getters instead.', __METHOD__));
        return $this->get($name);
    }

}
