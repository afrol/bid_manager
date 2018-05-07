<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "campaign_types".
 *
 * @property int $campaign_type_id
 * @property string $type_title
 */
class CampaignType extends ActiveRecord
{

    const TYPE_UNKNOWN = 1;
    const TYPE_TEXT_CAMPAIGN = 2;
    const TYPE_MOBILE_APP_CAMPAIGND = 3;
    const TYPE_DYNAMIC_TEXT_CAMPAIGN = 4;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'campaign_types';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type_title'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'campaign_type_id' => 'Campaign Type ID',
            'type_title' => 'Type Title',
        ];
    }
}
