<?php

namespace app\models\Enum;


class ScheduleStatusEnum
{

    const STATUS_PENDING = 1;

    /**
     * Send request to api
     */
    const STATUS_PROCESSING = 2;

    const STATUS_DONE = 3;

    /**
     * empty, incorrect bid
     */
    const STATUS_WARNING = 4;

    /**
     * fail app
     */
    const STATUS_ERROR = 5;

    /**
     * fail processing
     */
    const STATUS_FAIL = 6;

    const STATUS_DUPLICATE = 7;

    const STATUS_ROLL_OUT = 8;

    const STATUS_TEST = 9;

    const STATUS_NOT_FOUND = 10;

    public $id;

    public $name;

    private static $statusList = [
        1 => 'PENDING',
        2 => 'PROCESSING',
        3 => 'DONE',
        4 => 'WARNING',
        5 => 'ERROR',
        6 => 'FAIL',
        7 => 'DUPLICATE',
        8 => 'ROLL_OUT',
        9 => 'STATUS_TEST',
        10 => 'STATUS_NOT_FOUND',
    ];


    /**
     * @param $id
     * @return null|static
     */
    public static function findById($id)
    {
        return self::$statusList[$id] ?? null;
    }

    /**
     * @param $name
     * @return null|static
     */
    public static function findByName($name)
    {
        foreach (self::$statusList as $status) {
            if (strstr($status['name'], $name) === 0) {
                return $status;
            }
        }

        return null;
    }

    /**
     * @return array
     */
    public static function findAll()
    {
        return self::$statusList;
    }
}
