<?php

namespace app\components\YandexDirect;

use app\components\ServiceEvent;
use Yii;
use yii\base\Component;

abstract class ModelBase extends Component
{

    /**
     * @var  ApiYandex
     */
    public $yandex;

    /**
     * @var ServiceEvent
     */
    public $event;


    /**
     * @return int
     * @throws \Exception
     */
    abstract public function getData() : int;

    public function init()
    {
        $this->yandex = Yii::$app->yandex;
        $this->event = new ServiceEvent();
    }
}
