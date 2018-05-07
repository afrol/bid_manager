<?php

namespace app\models\Enum;


use yii\base\BaseObject;

class AdGroupTypesEnum extends BaseObject
{

    const DYNAMIC_TEXT_AD_GROUP = 1;

    const MOBILE_APP_AD_GROUP = 2;

    const TEXT_AD_GROUP = 3;

    public $id;

    public $name;

    private static $list = [
        1 => 'DYNAMIC_TEXT_AD_GROUP',
        2 => 'MOBILE_APP_AD_GROUP',
        3 => 'TEXT_AD_GROUP',
    ];

    public static function findById($id)
    {
        return isset(self::$list[$id]) ? new static(self::$list[$id]) : null;
    }

    public static function findByName($name)
    {
        foreach (self::$list as $status) {
            if (strstr($status['name'], $name) === 0) {
                return new static($status);
            }
        }

        return null;
    }

    public static function findAll()
    {
        return self::$list;
    }
}
