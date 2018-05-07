<?php

namespace app\components\Behaviors;

use app\models\AdGroup;
use app\models\Campaign;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Class RelateBehavior
 * @package app\components\Behaviors
 *
 * @property ActiveRecord $owner
 */
class RelateBehavior extends Behavior
{

    /* ActiveRelation */
    public function getGroup()
    {
        return $this->owner->hasOne(AdGroup::class, ['ad_group_id' => 'ad_group_id']);
    }

    /* ActiveRelation */
    public function getCampaign()
    {
        return $this->owner->hasOne(Campaign::class, ['campaign_id' => 'campaign_id']);
    }

    /* Getter for token id */
    public function getTokenId() {
        return $this->owner->campaign->api_token_id;
    }
}
