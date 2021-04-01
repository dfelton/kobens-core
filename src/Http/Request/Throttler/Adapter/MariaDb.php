<?php

namespace Kobens\Core\Http\Request\Throttler\Adapter;

use Kobens\Core\Exception\LogicException;
use Kobens\Core\Exception\Http\Request\Throttler\InvalidIdentifierException;
use Kobens\Core\Exception\Http\Request\Throttler\LockWaitTimeoutException;
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
 * MariaDb based Throttler Adapter.
 *
 * TODO: Accept Db credentials and instantiate our own instance.
 *   This ensures there is no question whether or not the adapter
 *   has its own connection to the database. Which is necessary
 *   for proper functionality. (we cannot allow other areas of
 *   the application to use the same instance and possibly have
 *   already started a transaction with the database.)
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
     * @var \Zend\Db\Adapter\Driver\ConnectionInterface
     */
    private $connection;

    private static bool $inTransaction = false;
    private static string $activeTransaction = '';

    /**
     * @param Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->connection = $adapter->driver->getConnection();
    }

    /**
     * {@inheritDoc}
     * @see \Kobens\Core\Http\Request\Throttler\AdapterInterface::get()
     */
    public function get(string $id): DataModelInterface
    {
        if (self::$inTransaction) {
            throw new LogicException(sprintf(
                'Throttling transaction already started. It is required that the active transaction be closed out with "%s::set(\'%s\') first.',
                self::class,
                self::$activeTransaction
            ));
        }
        self::$inTransaction = true;
        self::$activeTransaction = $id;
        $this->connection->beginTransaction();
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
        if (!self::$inTransaction) {
            throw new LogicException(spritf(
                'No active throttling transaction started. It is required that the count be explicitly fetched via "%s::get(\'%s\') first."',
                self::class,
                $id
            ));
        } elseif (self::$activeTransaction !== $id) {
            throw new LogicException(spritf(
                'Throttling transaction for "%s" must be closed out before attempting to set anything else.',
                self::$activeTransaction
            ));
        }
        $result = $this->adapter->query(
            'UPDATE `throttler` SET `count` = ?, `time` = ? WHERE `id` = ?',
            [$count, $time, $id]
        );
        self::$activeTransaction = '';
        self::$inTransaction = false;
        $this->connection->commit();
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
                if ($e->getMessage() !== self::LOCK_WAIT_MESSAGE) {
                    throw $e;
                } elseif ($attempts >= self::LOCK_WAIT_MAX_ATTEMPTS) {
                    throw new LockWaitTimeoutException('Lock wait timeout retry limit reached.', 0, $e);
                }
                ++$attempts;
            }
        } while ($data === false);
        return $data;
    }
}
