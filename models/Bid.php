<?php

namespace app\models;

use app\components\Behaviors\RelateBehavior;
use yii\db\ActiveRecord;
use app\components\Behaviors\BatchInsert;

/**
 * This is the model class for table "bids".
 *
 * @property int $autoincrement_id
 * @property int $ad_group_id
 * @property int $campaign_id
 * @property int $bid
 * @property int $context_bid
 * @property int $min_search_price
 * @property int $current_search_price
 * @property string $updated_at
 *
 * BatchInsert
 * @method int insertUpdate(array $dataInsert, $columns = null)
 * @method int insertIgnore(array $dataInsert, $columns = null)
 */
class Bid extends ActiveRecord
{

    const SCENARIO_SAVE = 'save';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bids';
    }

    public static function primaryKey()
    {
        return ['ad_group_id'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ad_group_id', 'campaign_id', 'bid', 'context_bid', 'min_search_price', 'current_search_price'], 'integer'],
            [['updated_at'], 'safe'],
        ];
    }

    public function behaviors()
    {
        return [
            BatchInsert::class,
            RelateBehavior::class
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
            'bid' => 'Bid',
            'context_bid' => 'Context Bid',
            'min_search_price' => 'Min Search Price',
            'current_search_price' => 'Current Search Price',
            'updated_at' => 'Updated At',

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        if ($this->getScenario() === self::SCENARIO_SAVE) {
            return ['ad_group_id', 'campaign_id', 'bid', 'context_bid', 'min_search_price', 'current_search_price'];
        }

        return parent::attributes();
    }
}
