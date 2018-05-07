<?php

namespace app\models;

use app\components\Behaviors\BatchInsert;
use app\models\Enum\AdGroupTypesEnum;
use app\models\Enum\StatusEnum;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "ad_groups".
 *
 * @property int $autoincrement_id
 * @property int $ad_group_id
 * @property int $campaign_id
 * @property string $name
 * @property int $type_id
 * @property int $status_id
 * @property string $updated_at
 *
 * BatchInsert
 * @method int insertUpdate(array $dataInsert, $columns = null)
 * @method int insertIgnore(array $dataInsert, $columns = null)
 */
class AdGroup extends ActiveRecord
{

    const SCENARIO_SAVE = 'save';

    const LIMIT_GROUPS = 1000;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ad_groups';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ad_group_id', 'campaign_id'], 'integer'],
            [['updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['type_id', 'status_id'], 'integer', 'max' => 4],
            [['ad_group_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'autoincrement_id' => 'Autoincrement ID',
            'ad_group_id' => 'Ad Groups ID',
            'campaign_id' => 'Campaign ID',
            'name' => 'Name',
            'type_id' => 'Type ID',
            'status_id' => 'Status ID',
            'updated_at' => 'Updated At',
        ];
    }

    public function behaviors()
    {
        return [
            BatchInsert::class
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        if ($this->getScenario() === self::SCENARIO_SAVE) {
            return ['ad_group_id', 'campaign_id', 'name', 'type_id', 'status_id'];
        }

        return parent::attributes();
    }

    public function getType()
    {
        return AdGroupTypesEnum::findAll()[$this->type_id] ?? null;
    }

    public function getStatus()
    {
        return StatusEnum::findAll()[$this->status_id] ?? null;
    }

    public function getCampaign()
    {
        return $this->hasOne(Campaign::class, ['campaign_id' => 'campaign_id']);
    }

    public function getCampaignByToken($tokenId)
    {
        return $this->hasOne(Campaign::class, ['campaign_id' => 'campaign_id'])
            ->where(['api_token_id' => $tokenId]);
    }

    /* ActiveRelation */
    public function getBid()
    {
        return $this->hasOne(Bid::class, ['ad_group_id' => 'ad_group_id']);
    }

    /* Getter for token id */
    public function getBidCost() {
        return $this->bid ? $this->bid->bid : null;
    }
}
