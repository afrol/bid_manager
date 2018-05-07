<?php

namespace app\components;


use yii\base\Event;

class ServiceEvent extends Event
{

    const LEVEL_ERROR = 1;
    const LEVEL_WARNING = 2;
    const LEVEL_INFO = 4;

    /**
     * @var string
     */
    public $info;

    /**
     * @var string
     */
    public $level;
}
