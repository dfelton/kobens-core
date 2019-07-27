<?php

namespace Kobens\Core\Http\Request;

// DROP TABLE IF EXISTS `throttler`;
// CREATE TABLE `throttler` (
//     `id` VARCHAR(255) NOT NULL COMMENT 'Key',
//     `max` INT(10) UNSIGNED NOT NULL COMMENT 'Limit',
//     `count` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Count',
//     `time` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Time',
//     PRIMARY KEY (`id`)
// ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Throttler';

final class Throttler
{
    private $id;
    private $adapter;

    public function __construct(string $id)
    {
        $this->adapter = \Kobens\Core\Db::getAdapter();
        if (!$this->isKeyValid($id)) {
            throw new \Exception ("Invalid throttle key '$id'");
        }
        $this->id = $id;
    }

    private function isKeyValid(string $id): bool
    {
        return $this->adapter->query('SELECT COUNT(1) FROM `throttler` WHERE `id` = ?', [$id])->count() === 1;
    }

    private function fetchForUpdate()
    {
        $data = $this->adapter->query('SELECT * FROM `throttler` WHERE `id` = ? FOR UPDATE', [$this->id])->toArray()[0];
        $data['max'] = (int) $data['max'];
        $data['count'] = (int) $data['count'];
        $data['time'] = (int) $data['time'];
        return $data;
    }

    private function update(int $count, int $time)
    {
        $this->adapter->query('UPDATE `throttler` SET `count` = ?, `time` = ? WHERE `id` = ?', [$count, $time, $this->id]);
    }

    public function throttle(): void
    {
        $this->adapter->driver->getConnection()->beginTransaction();
        $data = $this->fetchForUpdate();
        $count = $data['count'];
        $time = \time();
        switch (true) { // correct, there is no break statements
            case $data['time'] > $time:
                throw \Exception ('Throttler\'s last usage was in future. Please check system time settings shit is fucked.');
            case $data['time'] === $time && $data['count'] >= $data['max'];
                do {
                    \usleep(0010000); // 1/100th of a second
                    $time = \time();
                } while ($time === $data['time']);
                $count = 0;
            case $data['time'] === 0:
            case $data['time'] < $time;
                $count = 0;
            default:
                $count++;
        }

        $this->update($count, $time);
        $this->adapter->driver->getConnection()->commit();
    }
}
