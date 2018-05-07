<?php
namespace app\commands;

use app\components\CheckLogger;
use app\components\log\LogEnum;
use app\components\YandexDirect\CampaignService;
use yii\base\Event;
use yii\console\ExitCode;

class CampaignController extends BaseController
{

    public function init()
    {
        $this->logger = new CheckLogger(false, LogEnum::GREP_CAMPAIGN);

        Event::on(CampaignService::class, CampaignService::EVENT_PROCESSING, [$this, 'processingCommand']);
        Event::on(CampaignService::class, CampaignService::EVENT_PROCESSING, function ($event) {
            isset($event->info) && $this->logger->addLog($event->info);
        });
    }

    /**
     * This command grep Campaigns.
     * @return int
     */
    public function actionIndex()
    {
        $time = $this->beginCommand('Send request to Yandex Direct');

        try {
            $numberRows = (new CampaignService())->getData();
        } catch (\Exception $e) {
            \Yii::error($e->getMessage());
            return ExitCode::DATAERR;
        }

        $this->logger->addLog('Campaign processed, rows = ' . $numberRows);

        $this->endCommand($time, 'numberRows: ' . $numberRows);

        return ExitCode::OK;
    }
}
