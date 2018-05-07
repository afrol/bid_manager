<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "campaign_states".
 *
 * @property int $campaign_state_id
 * @property string $state_title
 */
class CampaignState extends ActiveRecord
{

    const STATE_UNKNOWN = 1;
    const STATE_ARCHIVED = 2;
    const STATE_CONVERTED = 3;
    const STATE_ENDED = 4;
    const STATE_OFF = 5;
    const STATE_ON = 6;
    const STATE_SUSPENDED = 7;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'campaign_states';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['state_title'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'campaign_state_id' => 'Campaign State ID',
            'state_title' => 'State Title',
        ];
    }
}
