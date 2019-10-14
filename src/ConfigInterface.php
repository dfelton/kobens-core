<?php

namespace Kobens\Core;

interface ConfigInterface
{
    public function getRootDir(): string;

    public function get(string $name);
}
