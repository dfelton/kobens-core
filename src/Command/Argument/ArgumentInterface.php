<?php

namespace Kobens\Core\Command\Argument;

interface ArgumentInterface
{
    public function getDefault();

    public function getDescription() : string;

    public function getMode() : int;

    public function getName() : string;
}
