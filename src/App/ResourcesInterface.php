<?php

namespace Kobens\Core\App;

interface ResourcesInterface
{
    /**
     * @return \Zend\Config\Config
     */
    public function getConfig() : \Zend\Config\Config;

    /**
     * @return \Kobens\Core\Output
     */
    public function getOutput() : \Kobens\Core\Output;

    /**
     * @return \Kobens\Core\Db\Adapter
     */
    public function getDb() : \Kobens\Core\Db\Adapter;
}