<?php

namespace app\commands;


use app\components\CheckLogger;
use app\components\log\LogEnum;
use Yii;
use yii\base\Event;
use yii\console\Controller;

ini_set('memory_limit', '2048M');
ini_set('default_socket_timeout', 600);

abstract class BaseController extends Controller
{

    public $compact = true;

    /**
     * @var CheckLogger
     */
    public $logger;


    /**
     * @param string $actionID
     * @return string[]
     */
    public function options($actionID)
    {
        return array_merge(parent::options($actionID), ['compact']);
    }

    /**
     * Prepares for a command to be executed, and outputs to the console.
     *
     * @param string $description the description for the command, to be output to the console.
     * @return float the time before the command is executed, for the time elapsed to be calculated.
     * @since 2.0.13
     */
    protected function beginCommand($description)
    {
        if (!$this->compact) {
            echo "    > $description ...";
        }
        return microtime(true);
    }

    /**
     * Finalizes after the command has been executed, and outputs to the console the time elapsed.
     *
     * @param float $time the time before the command was executed.
     * @param string $description
     * @since 2.0.13
     */
    protected function endCommand($time, $description = '')
    {
        if (!$this->compact) {
            echo $description;
            echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)" . PHP_EOL;
        }
    }

    public function processingCommand(Event $event)
    {
        if ($this->compact) {
            return null;
        }

        echo isset($event->info) ? $event->info . PHP_EOL : '.';
    }

    public function errorCommand(Event $event)
    {
        $this->logger($event, 1);
    }

    public function warningCommand(Event $event)
    {
        $this->logger($event, 3);
    }

    public function infoCommand(Event $event)
    {
        $this->logger($event, 4);
    }

    /**
     * @param Event $event
     * @param int $level
     * @return void
     */
    protected function logger(Event $event, $level = 4)
    {
        $msg = $event->info ?? '';

        switch ($level) {
            case 1: $method = 'error';
                break;
            case 3: $method = 'warning';
                break;
            case 4: $method = 'info';
                break;
            default: $method = 'info';
        }

        Yii::$method($msg, LogEnum::SCHEDULE);

        if ($this->compact) {
            echo '';
        }

        echo $msg . PHP_EOL;
    }

    protected function sendResponse(string $subject, string $body)
    {
        $mailManager = Yii::$app->components('mail_manager');
        mail($mailManager->getMails(['category' => 'cron', 'element' => 'live-crons']), $subject, $body);
    }
}
