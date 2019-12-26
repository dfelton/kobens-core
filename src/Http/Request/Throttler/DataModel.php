<?php

namespace Kobens\Core\Http\Request\Throttler;

final class DataModel implements DataModelInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var int
     */
    private $max;

    /**
     * @var int
     */
    private $count;

    /**
     * @var int
     */
    private $time;

    /**
     * @param int $id
     * @param int $max
     * @param int $count
     * @param int $time
     */
    public function __construct(string $id, int $max, int $count, int $time)
    {
        $this->id = $id;
        $this->max = $max;
        $this->count = $count;
        $this->time = $time;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getMax(): int
    {
        return $this->max;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getTime(): int
    {
        return $this->time;
    }
}
