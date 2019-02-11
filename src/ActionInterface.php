<?php

namespace Kobens\Core;

interface ActionInterface extends \Kobens\Core\Config\RuntimeInterface
{
    /**
     * @param \Kobens\Core\App\ResourcesInterface $resourcesInterface
     */
    public function __construct(
        \Kobens\Core\App\ResourcesInterface $resourcesInterface
    );

    /**
     * @return ActionInterface
     */
    public function execute() : ActionInterface;
}