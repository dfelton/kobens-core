<?php

namespace Kobens\Core;

interface SleeperInterface
{
    public function sleep(int $sleepTime, callable $stopCheck, int $stopCheckInterval = 5): void;
}
