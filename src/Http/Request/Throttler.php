<?php

namespace Kobens\Core\Http\Request;

use Kobens\Core\Exception\LogicException;

final class Throttler
{
    private static $throttles = [];

    private $key;

    public function __construct(string $key = null)
    {
        $this->key = $key;
    }

    public function getLimit(string $key) : ?int
    {
        return isset(self::$throttles[$key]) ? isset(self::$throttles[$key]['limit']) : null;
    }

    public function addThrottle(string $key, int $limitPerSecond) : void
    {
        if (isset(self::$throttles[$key])) {
            throw new LogicException("Throttle '$key' already exists.");
        } elseif ($limitPerSecond <= 0) {
            throw new LogicException('Limit per second must be greater than zero.');
        }
        static::$throttles[$key] = [
            'limit' => $limitPerSecond,
            'time' => null,
            'count' => 0
        ];
    }

    private function isSetup()
    {
        if (!\is_string($this->key) || !isset(self::$throttles[$this->key])) {
            throw new LogicException(\sprintf('Misconfigurationn error in "%s"', self::class));
        }
    }

    public function throttle() : void
    {
        $this->isSetup();
        $time = \time();
        $data = self::$throttles[$this->key];
        switch (true) { // correct, there is no break statements
            case $data['time'] === $time && $data['limit'] == $data['count'];
                do {
                    \usleep(50000);
                    $time = \time();
                } while ($time === $data['time']);
            case $data['time'] === null:
            case $data['time'] < $time;
                self::$throttles[$this->key]['time'] = $time;
                self::$throttles[$this->key]['count'] = 0;
            default:
                self::$throttles[$this->key]['count']++;
        }
    }
}