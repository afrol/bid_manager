<?php
namespace app\commands;

use app\components\CheckLogger;
use app\components\log\LogEnum;
use app\components\Schedule\Config;
use app\components\Schedule\ScheduleService;
use app\components\ServiceEvent;
use app\models\BidSchedule;
use app\models\Enum\ScheduleStatusEnum;
use yii\base\Event;
use yii\console\ExitCode;

class ScheduleController extends BaseController
{
    /**
     * @var int
     */
    public $dateRange;


    public function init()
    {
        Event::on(ScheduleService::class, ScheduleService::EVENT_PROCESSING, [$this, 'processingCommand']);
    }

    public function options($actionID)
    {
        return array_merge(parent::options($actionID), ['dateRange']);
    }

    public function optionAliases()
    {
        return [
            'date' => 'dateRange',
        ];
    }

    /**
     * This command generate new bid cost to bid_schedules.
     * @return int
     */
    public function actionCreate()
    {
        $time = $this->beginCommand('Start process');

        try {
            $numberRows = (new ScheduleService([
                'logger' => new CheckLogger(false, LogEnum::SCHEDULE),
                'event' => new ServiceEvent()
            ]))->createSchedule($this->dateRange ?? ScheduleService::LAST_3_DAYS);
        } catch (\Exception $e) {
            \Yii::error($e->getMessage());
            return ExitCode::DATAERR;
        }

        $this->endCommand($time, 'Generated records: ' . $numberRows);

        return ExitCode::OK;
    }

    /**
     * Upgrades the bids by api yandex.
     * @return int
     */
    public function actionUp()
    {
        $time = $this->beginCommand('Start process');

        $service = new ScheduleService([
            'logger' => new CheckLogger(true, LogEnum::SCHEDULE_UP),
            'event' => new ServiceEvent()
        ]);
        try {
            $numberRows = $service->up($this->dateRange ?? ScheduleService::TODAY);
        } catch (\Exception $e) {
            \Yii::error($e->getMessage());
            return ExitCode::DATAERR;
        }

        $this->endCommand($time, 'Generated records: ' . $numberRows);

        return ExitCode::OK;
    }

    /**
     * Downgrades the bids by reverting old processing.
     * @return int
     */
    public function actionDown()
    {
        $time = $this->beginCommand('Start process');

        $service = new ScheduleService([
            'logger' => new CheckLogger(true, LogEnum::SCHEDULE_DOWN),
            'event' => new ServiceEvent()
        ]);
        try {
            $numberRows = $service->down($this->dateRange ?? ScheduleService::TODAY);
        } catch (\Exception $e) {
            \Yii::error($e->getMessage());
            return ExitCode::DATAERR;
        }

        $this->endCommand($time, 'Generated records: ' . $numberRows);

        return ExitCode::OK;
    }

    /**
     * This command for request test to api
     *
     * @param int $date
     * @param string $action
     * @param array $ids
     * @return int
     * @throws \Exception
     */
    public function actionTest(int $date, string $action, array $ids)
    {
        $this->compact = false;

        $time = $this->beginCommand('Start process');

        switch ($action) {
            case 'up':
                    $statusId = ScheduleStatusEnum::STATUS_DONE;
                    $field = BidSchedule::FIELD_BID_PROCESSED;
                break;
            case 'down':
                    $statusId = ScheduleStatusEnum::STATUS_ROLL_OUT;
                    $field = BidSchedule::FIELD_BID;
                break;
            default: throw new \InvalidArgumentException('Action invalid or empty');
        }

        $service = new ScheduleService([
            'logger' => new CheckLogger(true, LogEnum::SCHEDULE_TEST),
            'event' => new ServiceEvent(),
            'config' => new Config([
                'dateRange' => $date,
                'filters' => [['testGroup', $ids]],
                'statusId' => $statusId,
                'field' => $field
            ])
        ]);

        $numberRows = $service->doApi();

        $this->endCommand($time, 'Generated records: ' . $numberRows);

        return ExitCode::OK;
    }
}
