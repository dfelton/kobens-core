<?php

namespace Kobens\Core;

class Output
{
    public function write(string $message) : Output
    {
        echo $message,PHP_EOL;
        return $this;
    }

    public function writeException(\Exception $e) : Output
    {
        echo $e->getMessage(),PHP_EOL;
        return $this;
    }
}