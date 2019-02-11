<?php

namespace Kobens\Core\App;

class Resources implements ResourcesInterface
{
    /**
     * @var \Kobens\Core\Db\Adapter
     */
    protected $db;

    /**
     * @var \Zend\Config\Config
     */
    protected $config;

    /**
     * @var \Kobens\Core\Output
     */
    protected $output;

    public function __construct(
        \Kobens\Core\Db\Adapter $adapter,
        \Kobens\Core\Output $output,
        \Zend\Config\Config $config
    ) {
        $this->db = $adapter;
        $this->config = $config;
        $this->output = $output;
    }

    public function getConfig() : \Zend\Config\Config
    {
        return $this->config;
    }

    public function getOutput() : \Kobens\Core\Output
    {
        return $this->output;
    }

    public function getDb() : \Kobens\Core\Db\Adapter
    {
        return $this->db;
    }
}