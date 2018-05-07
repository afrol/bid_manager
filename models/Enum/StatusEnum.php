<?php

namespace app\models\Enum;


use yii\base\BaseObject;

class StatusEnum extends BaseObject
{

    const STATUS_UNKNOWN = 1;

    const STATUS_ACCEPTED = 2;

    const STATUS_DRAFT = 3;

    const STATUS_MODERATION = 4;

    const STATUS_PREACCEPTED = 5;

    const STATUS_REJECTED = 6;

    public $id;

    public $name;

    private static $statusList = [
        1 => 'UNKNOWN',
        2 => 'ACCEPTED',
        3 => 'DRAFT',
        4 => 'MODERATION',
        5 => 'PREACCEPTED',
        6 => 'REJECTED',
    ];

    public static function findById($id)
    {
        return isset(self::$statusList[$id]) ? new static(self::$statusList[$id]) : null;
    }

    public static function findByName($name)
    {
        foreach (self::$statusList as $status) {
            if (strstr($status['name'], $name) === 0) {
                return new static($status);
            }
        }

        return null;
    }

    public static function findAll()
    {
        return self::$statusList;
    }
}
