<?php

namespace Kobens\Core\Http\Request\Throttler\Adapter;

use Kobens\Core\Exception\LogicException;
use Kobens\Core\Exception\Http\Request\Throttler\InvalidIdentifierException;
use Kobens\Core\Http\Request\Throttler\AdapterInterface;
use Kobens\Core\Http\Request\Throttler\DataModel;
use Kobens\Core\Http\Request\Throttler\DataModelInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Exception\InvalidQueryException;

// DROP TABLE IF EXISTS `throttler`;
// CREATE TABLE `throttler` (
//     `id` VARCHAR(255) NOT NULL COMMENT 'Key',
//     `max` INT(10) UNSIGNED NOT NULL COMMENT 'Limit',
//     `count` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Count',
//     `time` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Time',
//     PRIMARY KEY (`id`)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Throttler';

/**
 * TODO Maybe some verification during instantiation of necessary MariaDb environment variables?
 * TODO If we configure the throttler to use it's own connection, maybe skip the in_transaction check?
 */
final class MariaDb implements AdapterInterface
{
    private const LOCK_WAIT_MAX_ATTEMPTS = 5;
    private const LOCK_WAIT_MESSAGE = 'Statement could not be executed (HY000 - 1205 - Lock wait timeout exceeded; try restarting transaction)';

    /**
     * @var Adapter
     */
    private $adapter;

    /**
     * @param Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * {@inheritDoc}
     * @see \Kobens\Core\Http\Request\Throttler\AdapterInterface::get()
     */
    public function get(string $id): DataModelInterface
    {
        if ($this->isInTransaction()) {
            throw new LogicException('Cannot get data while in transaction.');
        }
        $this->adapter->driver->getConnection()->beginTransaction();
        $data = $this->getRecord($id);
        if ($data === null) {
            throw new InvalidIdentifierException("Invalid Throttler ID '{$id}'.");
        }
        return new DataModel($data->id, (int) $data->max, (int) $data->count, (int) $data->time);
    }

    /**
     * {@inheritDoc}
     * @see \Kobens\Core\Http\Request\Throttler\AdapterInterface::set()
     */
    public function set(string $id, int $count, int $time): void
    {
        if (!$this->isInTransaction()) {
            throw new LogicException('Can only set data while in transaction.');
        }
        $result = $this->adapter->query(
            'UPDATE `throttler` SET `count` = ?, `time` = ? WHERE `id` = ?',
            [$count, $time, $id]
        );
        $this->adapter->driver->getConnection()->commit();
    }

    /**
     * @return bool
     */
    private function isInTransaction(): bool
    {
        $stmt = $this->adapter->query('SHOW VARIABLES LIKE "in_transaction"')->execute()->current();
        return (bool) (int) $stmt['Value'];
    }

    /**
     * @param string $id
     * @throws InvalidQueryException
     * @return \ArrayObject|NULL
     */
    private function getRecord(string $id): ?\ArrayObject
    {
        $data = false;
        $attempts = 0;
        do {
            try {
                $data = $this->adapter->query('SELECT * FROM `throttler` WHERE `id` = ? FOR UPDATE', [$id])->current();
            } catch (InvalidQueryException $e) {
                if (   $e->getMessage() !== self::LOCK_WAIT_MESSAGE
                    || $attempts >= self::LOCK_WAIT_MAX_ATTEMPTS
                ) {
                    throw $e;
                }
                ++$attempts;
            }
        } while ($data === false);
        return $data;
    }

}
