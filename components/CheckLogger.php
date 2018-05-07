<?php

namespace app\components;


use DateTime;
use Yii;

class CheckLogger
{

    /**
     * @var bool
     */
    private $full;

    /**
     * @var array
     */
    private $log = [];

    /**
     * @var int
     */
    private $startTimeStamp;

    /**
     * @var string
     */
    private $category;


    /**
     * @param bool $full
     * @param string $category
     */
    public function __construct(bool $full, string $category)
    {
        $this->full = $full;
        if ($full) {
            $this->log[] = 'MEMORY' . "\t" . 'MICROTIME' . "\t" . 'MESSAGE';
        }

        $this->category = $category;

        $this->startTimeStamp = (new DateTime())->getTimestamp();
    }

    /**
     * @param string $message
     */
    public function addLog(string $message)
    {
        if ($this->full) {
            $message = memory_get_usage() . "\t" . microtime(true) . "\t" . $message;
        }
        $this->log[] = $message;
    }

    /**
     * @param callable $callback
     * @param array $params
     */
    public function addLogFull(callable $callback, array $params = [])
    {
        if (!$this->full) {
            return;
        }
        $this->addLog(call_user_func_array($callback, $params));
    }

    /**
     * @return array
     */
    public function getLog() : array
    {
        $this->log[] = sprintf(
            'ExecutionTime is %s second(s)',
            round((new DateTime())->getTimestamp() - $this->startTimeStamp, 4)
        );
        return $this->log;
    }

    public function __destruct()
    {
        if (!empty($this->log)) {
            Yii::info($this->getLog(), $this->category);
        }
    }
}
