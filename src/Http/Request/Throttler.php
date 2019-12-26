<?php

namespace Kobens\Core\Http\Request;

use Kobens\Core\Http\Request\Throttler\AdapterInterface;

final class Throttler implements ThrottlerInterface
{
    /**
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * @var string
     */
    private $id;

    /**
     * @param AdapterInterface $adapter
     * @param string $id
     */
    public function __construct(AdapterInterface $adapter, string $id)
    {
        $this->adapter = $adapter;
        $this->id = $id;
    }

    /**
     * {@inheritDoc}
     * @see \Kobens\Core\Http\Request\ThrottlerInterface::throttle()
     */
    public function throttle(): void
    {
        $data = $this->adapter->get($this->id);
        $count = $data->getCount();
        $time = \time();
        switch (true) { // correct, there is no break statements
            case $data->getTime() > $time:
                throw \Exception("Last usage for throttler ID '{$this->id}' is in the future. Please check system time settings shit is fucked.");
            case $data->getTime() === $time && $data->getCount() >= $data->getMax();
                do {
                    \usleep(0010000); // 1/100th of a second
                    $time = \time();
                } while ($time === $data->getTime());
                $count = 0;
            case $data->getTime() < $time;
            case $data->getTime() === 0:
                $count = 0;
            default:
                $count++;
        }
        $this->adapter->set($data->getId(), $count, $time);
    }
}
