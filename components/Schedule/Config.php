<?php

namespace app\components\Schedule;

use app\models\BidSchedule;
use app\models\ModelQuery;
use yii\base\InvalidArgumentException;
use yii\base\BaseObject;
use yii\base\InvalidCallException;

class Config extends BaseObject
{

    /**
     * @var int
     */
    private $statusId;

    /**
     * @var int
     */
    public $dateRange = ScheduleService::LAST_3_DAYS;

    /**
     * @var int
     */
    public $batchSize = 100;

    /**
     * @var array
     */
    private $filters = [];

    /**
     * @var string
     */
    private $field;


    /**
     * @param string $field
     */
    public function setField(string $field): void
    {
        if (!in_array($field , (new BidSchedule())->attributes())) {
            throw new InvalidCallException('Field unknown property: ' . BidSchedule::class . '::' . $field);
        }

        $this->field = $field;
    }

    /**
     * @return string|null
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param array $filters
     */
    public function setFilters(array $filters): void
    {
        foreach ($filters as $filter) {
            if (!method_exists(ModelQuery::class, $filter[0])) {
                throw new InvalidCallException('Filter unknown property: ' . ModelQuery::class . '::' . $filter);
            }

            $this->filters[] = $filter;
        }
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @return int
     */
    public function getStatusId(): int
    {
        if (!$this->statusId) {
            throw new InvalidArgumentException('StatusId empty');
        }
        return $this->statusId;
    }

    /**
     * @param int $statusId
     */
    public function setStatusId(int $statusId): void
    {
        $this->statusId = $statusId;
    }
}
