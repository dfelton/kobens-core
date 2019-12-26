<?php

namespace Kobens\Core\Http\Request\Throttler;

interface AdapterInterface
{
    /**
     * Fetch current data for throttler.
     *
     * Adapter's get() must also be responsible for inacting
     * a lock which prevents any concurrent jobs from
     * returning the Data Model, while a job which has received
     * the return for get() has yet to call set().
     *
     * @param string $id
     * @return DataModelInterface
     */
    public function get(string $id): DataModelInterface;

    /**
     * Set current data for the throttler.
     *
     * Adapter's set() must also be responsible for releasing the
     * lock created by get().
     *
     * @param int $id
     * @param int $count
     * @param int $time
     */
    public function set(string $id, int $count, int $time): void;
}
