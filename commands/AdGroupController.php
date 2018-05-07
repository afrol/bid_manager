<?php
namespace app\commands;

use app\components\CheckLogger;
use app\components\log\LogEnum;
use yii\base\Event;
use yii\console\ExitCode;

use app\components\YandexDirect\AdGroupService;

class AdGroupController extends BaseController
{

    public function init()
    {
        $this->logger = new CheckLogger(false, LogEnum::GREP_GROUP);

        Event::on(AdGroupService::class, AdGroupService::EVENT_PROCESSING, [$this, 'processingCommand']);
        Event::on(AdGroupService::class, AdGroupService::EVENT_PROCESSING, function ($event) {
            isset($event->info) && $this->logger->addLog($event->info);
        });
    }

    /**
     * This command grep AdGroup.
     * @return int
     */
    public function actionIndex()
    {
        $time = $this->beginCommand('Send request to Yandex Direct');

        try {
            $numberRows = (new AdGroupService())->getData();
        } catch (\Exception $e) {
            \Yii::error($e->getMessage());
            return ExitCode::DATAERR;
        }

        $this->endCommand($time, 'numberRows: ' . $numberRows);

        return ExitCode::OK;
    }
}
