<?php

namespace app\components\Schedule;

use app\components\ApiUser;
use app\components\CheckLogger;
use app\components\Dwh\Statistic;
use app\components\log\LogEnum;
use app\components\ServiceEvent;
use app\components\YandexDirect\ApiYandex;
use app\models\BidSchedule;
use app\models\Enum\ScheduleStatusEnum;
use app\models\ModelQuery;
use app\models\Rule;
use app\models\RuleCondition;
use Biplane\YandexDirect\Api\V5\Contract\BidSetItem;
use InvalidArgumentException;
use LogicException;
use Yii;
use yii\base\Component;
use yii\base\Event;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\StaleObjectException;

class ScheduleService extends Component
{

    const TODAY = 0;
    const YESTERDAY = 1;
    const LAST_3_DAYS = 3;
    const LAST_5_DAYS = 5;
    const LAST_7_DAYS = 7;
    const LAST_14_DAYS = 14;
    const LAST_30_DAYS = 30;

    const EVENT_PROCESSING = self::class;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var CheckLogger
     */
    public $logger;

    /**
     * @var ServiceEvent
     */
    public $event;


    public function init()
    {
        Event::on(self::class, self::EVENT_PROCESSING, function ($event) {
            isset($event->info) && $this->logger->addLog($event->info);
        });
    }

    /**
     * @param int $dateRange
     * @return int
     * @throws \Exception
     */
    public function createSchedule(int $dateRange)
    {
        /** @var ActiveQuery[] $queryRules */
        $queryRules = (new Rule())->applyRule(
            (new Statistic())->createQuery($dateRange)
        );

        $ruleCondition = new RuleCondition();

        $list = [];

        /** @var ActiveQuery $queryRule */
        foreach ($queryRules as $ruleId => $queryRule) {
            $this->event->info = 'Process rule id: ' . $ruleId;
            $this->trigger(self::EVENT_PROCESSING, $this->event);
            $this->logger->addLog('Dump query sql = ' . $queryRule->createCommand()->rawSql);

            $value = $ruleCondition->getValueByRuleId($ruleId);
            if (!$value) {
                $this->logger->addLog('Empty value rule id =' . $ruleId);
                continue;
            }

            $result = $queryRule->all();
            if (!$result) {
                $this->logger->addLog('Empty result rule id =' . $ruleId);
                continue;
            }

            $list = array_merge($list, $this->adapterData($ruleId, $result, $ruleCondition));
        }

        $amountBids = count($list);
        $this->logger->addLog('Find bids amount: ' . $amountBids);

        if (!$amountBids) {
            throw new LogicException('Empty result by rule query');
        }

        $this->event->info = 'Insert to schedule batch: ' . $amountBids;
        $this->trigger(self::EVENT_PROCESSING, $this->event);
        $model = new BidSchedule(['scenario' => BidSchedule::SCENARIO_SAVE]);
        $rows = $model->batchInsert($list, $model->attributes());

        $this->logger->addLog('Save bid to schedule = ' . $rows);
        return $rows;
    }

    /**
     * @param int $dateRange
     * @return int
     * @throws \Exception
     */
    public function up(int $dateRange = self::TODAY)
    {
        return $this->setConfig(new Config([
            'dateRange' => $dateRange,
            'filters' => [['up']],
            'statusId' => ScheduleStatusEnum::STATUS_DONE,
            'field' => BidSchedule::FIELD_BID_PROCESSED
        ]))->doApi();
    }

    /**
     * @param int $dateRange
     * @return int
     * @throws \Exception
     */
    public function down(int $dateRange = self::TODAY)
    {
        return $this->setConfig(
            new Config([
                'dateRange' => $dateRange,
                'filters' => [['down']],
                'statusId' => ScheduleStatusEnum::STATUS_ROLL_OUT,
                'field' => BidSchedule::FIELD_BID
            ])
        )->doApi();
    }

    public function getSql()
    {
        $sqlList = [];
        /** @var ActiveQuery[] $queryRules */
        $queryRules = (new Rule())->applyRule(
            (new Statistic())->createQuery($this->config->dateRange)
        );

        /** @var ActiveQuery $queryRule */
        foreach ($queryRules as $ruleId => $queryRule) {
            $sqlList[$ruleId] = [
                'rule_id' => $ruleId,
                'query' => $queryRule->createCommand()->rawSql
            ];
        }

        return $sqlList;
    }

    /**
     * @param $ruleId
     * @return mixed
     */
    public function getSqlByRuleId($ruleId)
    {
        return $this->getSql()[$ruleId];
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function doApi()
    {
        /** @var ApiYandex $yandex */
        $yandex = Yii::$app->yandex;

        $clientList = $yandex->getClients();
        if (!$clientList) {
            throw new \Exception('Empty Yandex client');
        }

        /** @var ModelQuery $commonQuery */
        $commonQuery = (new BidSchedule())
            ->createQuery($this->config->dateRange);

        if ($this->config->getFilters()) {
            $commonQuery->applyFilters($this->config->getFilters());
        }

        $rows = 0;
        /** @var ApiUser $client */
        foreach ($clientList as $client) {
            $this->event->info = 'Process token id: ' . $client->getTokenId();
            $this->trigger(self::EVENT_PROCESSING, $this->event);

            $query = clone $commonQuery;
            $query->andTokenId($client->getTokenId());

            $this->logger->addLog('Dump query sql = ' . $query->createCommand()->rawSql);

            $numberRecords = $query->count();
            $this->logger->addLog('Checks TokenId ' . $client->getTokenId() . ', numberRecords=' . $numberRecords);
            if (!$numberRecords) {
                $this->logger->addLog('Checks list empty, nothing to do');
                continue;
            }

            $yandex->setCurrentClient($client);

            foreach ($query->batch($this->config->batchSize) as $models) {
                if ($models) {
                    $bidItems = $this->createBidItems($models);
                    if ($bidItems) {
                        $this->event->info = 'Request to API, number bidItems=' . count($bidItems);
                        $this->trigger(self::EVENT_PROCESSING, $this->event);

                        $result = [];
                        try {
                            /** @var BidSchedule->ad_group_id[] $result */
                            $result = $yandex->setBids($bidItems);
                        } catch (\Exception $e) {
                            $this->logger->addLog($e->getMessage());
                        }

                        $this->logger->addLog('Response API, number =' . count($result)
                            . print_r($result, true));
                        $rows += $this->saveModels($result, $models);
                    }
                }
            }
        }

        $this->logger->addLog('Update models, number =' . $rows);
        return $rows;
    }

    /**
     * @param int $ruleId
     * @param array $data
     * @param RuleCondition $ruleCondition
     * @return array
     */
    private function adapterData($ruleId, $data, RuleCondition $ruleCondition) : array
    {
        $value = $ruleCondition->getValueByRuleId($ruleId);
        if (!$value) {
            return [];
        }

        return array_map(function($a) use ($ruleId, $ruleCondition, $value) {
            $attributes = [
                'bid' => $a['bid']['bid'] ?? 0,
                'avg_cpc' => $a['avg_cpc'],
            ];

            $item = [
                'ad_group_id' => $a['ad_group_id'],
                'rule_id' => $ruleId,
                'bid' => $attributes['bid']
            ];

            $amount = $ruleCondition->getNewBidAmount($attributes, $value);

            if ($amount) {
                $item[BidSchedule::FIELD_BID_PROCESSED] = $amount;
                $item['status_id'] = ScheduleStatusEnum::STATUS_PENDING;
            } else {
                $item[BidSchedule::FIELD_BID_PROCESSED] = 0;
                $item['status_id'] = ScheduleStatusEnum::STATUS_NOT_FOUND;

                Yii::error('Empty bid amount ' . print_r($item, true), LogEnum::SCHEDULE_BID_NOT_FOUND);
            }

            $item['created_at'] = new Expression('NOW()');
            Yii::info($a, LogEnum::BID_LOG);

            return $item;
        }, $data);
    }

    /**
     * @param BidSchedule[] $models
     * @return BidSetItem[]
     */
    private function createBidItems(array $models)
    {
        $bidItems = [];

        /** @var BidSchedule $model */
        foreach ($models as $model) {
            $model->status_id = ScheduleStatusEnum::STATUS_PROCESSING;
            try {
                $model->update(false);
            } catch (StaleObjectException | \Exception | \Throwable $e) {
                Yii::error($e->getMessage());
            }

            $bidItems[] = BidSetItem::create()
                ->setAdGroupId($model->ad_group_id)
                ->setBid($this->getAmountBid($model));
        }

        return $bidItems;
    }


    /**
     * @param array $result
     * @param $models
     * @return int
     */
    private function saveModels(array $result, $models)
    {
        $rows = 0;

        /** @var BidSchedule $model */
        foreach ($models as $model) {
            if ($result && in_array($model->ad_group_id, $result)) {
                $model->status_id = $this->config->getStatusId();

                $model->bids->bid = $this->getAmountBid($model);
                $model->bids->update(false);

                ++$rows;
            } else {
                $model->status_id = ScheduleStatusEnum::STATUS_ERROR;
                Yii::error(['ad_group_id' => $model->ad_group_id],LogEnum::SCHEDULE_API);
            }

            try {
                $model->update(false);
            } catch (StaleObjectException | \Exception | \Throwable $e) {
                Yii::error($e->getMessage());
            }
        }

        return $rows;
    }

    /**
     * @param BidSchedule $model
     * @return int|\Exception
     */
    private function getAmountBid(BidSchedule $model)
    {
        if ($this->config->getField()) {
            return $model->{$this->config->getField()};
        }

        throw new InvalidArgumentException('Field empty');
    }

    /**
     * @param Config $config
     * @return ScheduleService
     */
    public function setConfig(Config $config): self
    {
        $this->config = $config;
        return $this;
    }
}
