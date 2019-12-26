<?php

namespace Kobens\Core\Http\Request\Throttler;

interface DataModelInterface
{
    /**
     * @var string
     */
    public function getId(): string;

    /**
     * @var int
     */
    public function getMax(): int;

    /**
     * @var int
     */
    public function getCount(): int;

    /**
     * @var int
     */
    public function getTime(): int;
}
