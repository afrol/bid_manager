<?php

namespace app\models;


use app\models\Enum\ScheduleStatusEnum;
use yii\db\ActiveQuery;

class ModelQuery extends ActiveQuery
{

    /**
     * @param int $tokenId
     * @return ActiveQuery
     */
    public function andTokenId(int $tokenId)
    {
        return $this->andWhere([Campaign::tableName() . '.api_token_id' => $tokenId]);
    }

    /**
     * @return ActiveQuery
     */
    public function up()
    {
        return $this->andWhere([BidSchedule::tableName() . '.status_id' => ScheduleStatusEnum::STATUS_PENDING]);
    }

    /**
     * @return ActiveQuery
     */
    public function down()
    {
        return $this->andWhere([BidSchedule::tableName() . '.status_id' => ScheduleStatusEnum::STATUS_DONE]);
    }

    public function testGroup(array $groupIds)
    {
        return $this->andWhere([BidSchedule::tableName() . '.ad_group_id' => $groupIds]);
    }

    public function applyFilters(array $filters)
    {
        foreach ($filters as $filter) {
            $this->{$filter[0]}($filter[1] ?? null);
        }
    }
}
