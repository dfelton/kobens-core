<?php

namespace Kobens\Core\App;

use Kobens\Core\Db\Adapter;
use Kobens\Core\Output;
use Zend\Config\Config;

class Resources implements ResourcesInterface
{
    /**
     * @var Adapter
     */
    protected $db;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Output
     */
    protected $output;

    public function __construct(
        Output $output,
        Config $config
    ) {
        $this->db = new Adapter($config->get('database')->toArray());
        $this->config = $config;
        $this->output = $output;
    }

    public function getConfig() : Config
    {
        return $this->config;
    }

    public function getOutput() : Output
    {
        return $this->output;
    }

    public function getDb() : Adapter
    {
        return $this->db;
    }

}