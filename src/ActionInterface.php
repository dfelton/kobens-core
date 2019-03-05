<?php

namespace Kobens\Core;

interface ActionInterface extends \Kobens\Core\Config\RuntimeInterface
{
    public function execute() : void;
}