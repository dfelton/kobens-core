<?php

namespace Kobens\Core\Db;

use Zend\Db\Adapter\Adapter as ZendAdapter;
use Zend\Db\Sql\Sql;

class Adapter
{
    /**
     * @var ZendAdapter
     */
    private $adapter;

    public function __construct($config)
    {
        $this->adapter = new ZendAdapter($config);
    }

    /**
     * Return a new Sql object
     */
    public function getSql()
    {
        return new Sql($this->getAdapter());
    }

    public function getAdapter() : ZendAdapter
    {
        return $this->adapter;
    }
}