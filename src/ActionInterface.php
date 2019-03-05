<?php

namespace Kobens\Core;

interface ActionInterface extends \Kobens\Core\Config\RuntimeInterface
{
    public function __construct(
        \Kobens\Core\App\ResourcesInterface $resourcesInterface
    );

    public function execute() : void;
}