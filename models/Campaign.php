<?php

namespace app\models;

use yii\db\ActiveRecord;
use app\components\Behaviors\BatchInsert;

/**
 * This is the model class for table "campaigns".
 *
 * @property int $autoincrement_id
 * @property int $campaign_id
 * @property int $api_token_id
 * @property string $name
 * @property int $type_id
 * @property int $state_id
 * @property string $updated_at
 *
 * BatchInsert
 * @method int insertUpdate(array $dataInsert, $columns = null)
 * @method int insertIgnore(array $dataInsert, $columns = null)
 */
class Campaign extends ActiveRecord
{

    const SCENARIO_SAVE = 'save';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'campaigns';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['campaign_id', 'api_token_id'], 'integer'],
            [['updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['type_id', 'state_id'], 'string', 'max' => 4],
            [['campaign_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'autoincrement_id' => 'Autoincrement ID',
            'campaign_id' => 'Campaign ID',
            'api_token_id' => 'Api Token ID',
            'name' => 'Name',
            'type_id' => 'Type ID',
            'state_id' => 'State ID',
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
           return ['campaign_id', 'api_token_id', 'name', 'type_id', 'state_id'];
       }

       return parent::attributes();
    }

    /* ActiveRelation */
    public function getCampaignState()
    {
       return $this->hasOne(CampaignState::class, ['campaign_state_id' => 'state_id']);
    }

    /* Getter for state_title */
    public function getStateTitle()
    {
       return $this->CampainState->state_title;
    }

    /* ActiveRelation */
    public function getCampaignType()
    {
        return $this->hasOne(CampaignType::class, ['campaign_type_id' => 'type_id']);
    }

    /* Getter for type_title */
    public function getTypeTitle()
    {
        return $this->CampaignType->type_title;
    }
}
