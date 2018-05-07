<?php

namespace app\components\Behaviors;


use Closure;
use yii\base\Behavior;

class DateBehavior extends Behavior
{

    const DATE_FORMANT = 'Y-m-d';

    /**
     * @var string
     */
    public $time = 'now';

    /**
     * @param int $day
     * @return string
     */
    public function createDatePeriod(int $day) : string
    {
        $date = new \DateTime($this->getTime());

        if ($day) {
            $date->modify('-' . $day . ' day');
        }

        return $date->format(self::DATE_FORMANT);
    }

    /**
     * @return string
     */
    public function getTime(): string
    {
        if ($this->time instanceof Closure || (is_array($this->time) && is_callable($this->time))) {
            return call_user_func($this->time);
        }

        return $this->time;
    }
}
