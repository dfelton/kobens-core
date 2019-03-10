<?php

namespace Kobens\Core;

use Zend\Db\Adapter\Adapter;

class Db
{
    public function getAdapter() : Adapter
    {
        return new Adapter((new Config())->database->toArray());
    }
}