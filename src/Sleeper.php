<?php

namespace Kobens\Core;

/**
 * Provides a method for sleeping a specified number of seconds,
 * while performing an early return should a condition be met
 * prior to completion of the full number of seconds to sleep.
 */
final class Sleeper implements SleeperInterface
{
    public function sleep(int $sleepTime, callable $stopCheck, int $stopCheckInterval = 5): void
    {
        $now = \time();
        $endTime = $now + $sleepTime;
        while ($now < $endTime && $stopCheck() !== true) {
            $remaining = $endTime - $now;
            \sleep($remaining < $stopCheckInterval ? $remaining : $stopCheckInterval);
            $now = \time();
        }
    }
}
