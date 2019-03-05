<?php

namespace Kobens\Core\App;

interface ResourcesInterface
{
    public function getConfig() : \Zend\Config\Config;

    public function getOutput() : \Kobens\Core\Output;

    public function getDb() : \Kobens\Core\Db\Adapter;
}