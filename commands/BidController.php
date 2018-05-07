<?php
namespace app\commands;

use app\components\CheckLogger;
use app\components\log\LogEnum;
use app\components\YandexDirect\BidService;
use yii\base\Event;
use yii\console\ExitCode;

class BidController extends BaseController
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->logger = new CheckLogger(false, LogEnum::GREP_BID);

        Event::on(BidService::class, BidService::EVENT_PROCESSING, [$this, 'processingCommand']);
        Event::on(BidService::class, BidService::EVENT_PROCESSING, function ($event) {
            isset($event->info) && $this->logger->addLog($event->info);
        });
    }

    /**
     * This command grep Bid.
     * @return int
     */
    public function actionIndex()
    {
        $time = $this->beginCommand('Send request to Yandex Direct');

        try {
            $numberRows = (new BidService())->getData();
        } catch (\Exception $e) {
            \Yii::error($e->getMessage());
            return ExitCode::DATAERR;
        }

        $this->logger->addLog('Bid processed, rows = ' . $numberRows);

        $this->endCommand($time, 'numberRows: ' . $numberRows);

        return ExitCode::OK;
    }
}
