<?php

namespace app\models;

use app\components\Behaviors\BatchInsert;
use app\components\Behaviors\DateBehavior;
use app\components\Behaviors\RelateBehavior;
use app\models\Enum\ScheduleStatusEnum;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "bid_schedules".
 *
 * @property int $autoincrement_id
 * @property int $ad_group_id
 * @property int $rule_id
 * @property int $bid
 * @property int $bid_processed
 * @property int $status_id
 * @property string $created_at
 * @property string $updated_at
 *
 * DateBehavior
 * @method self createDatePeriod(int $day) : string
 *
 * BatchInsert
 * @method int batchInsert(array $dataInsert, $columns = null)
 *
 * RelateBehavior
 * @method ActiveQuery getCampaign()
 * @method ActiveQuery getGroup()
 * @method string getTokenId()
 *
 * ModelQuery
 * @method ActiveQuery andTokenId(int $tokenId)
 */
class BidSchedule extends ActiveRecord
{

    const FIELD_BID = 'bid';
    const FIELD_BID_PROCESSED = 'bid_processed';

    /**
     * @var integer
     */
    public $api_token_id;

    const SCENARIO_SAVE = 'save';
    const SCENARIO_SEARCH = 'search';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bid_schedules';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ad_group_id', 'rule_id', 'bid', 'bid_processed'], 'integer'],
            [['ad_group_id', 'rule_id', 'bid', 'bid_processed','created_at'], 'required', 'on' => Model::SCENARIO_DEFAULT],
            [['ad_group_id', 'rule_id', 'status_id','created_at'], 'safe', 'on' => self::SCENARIO_SEARCH],
            [['status_id'], 'integer', 'max' => 4],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'autoincrement_id' => 'Autoincrement ID',
            'ad_group_id' => 'Ad Group ID',
            'rule_id' => 'Rule ID',
            'bid' => 'Bid',
            'bid_processed' => 'Bid Processed',
            'status_id' => 'Status ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            BatchInsert::class,
            DateBehavior::class,
            RelateBehavior::class,
            [
                'class' => TimestampBehavior::class,
                'skipUpdateOnClean' => true,
                'createdAtAttribute' => 'created_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function find()
    {
        return new ModelQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_SAVE] = ['ad_group_id', 'rule_id', 'bid', 'bid_processed', 'status_id', 'created_at'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        if ($this->getScenario() === self::SCENARIO_SAVE) {
            return ['ad_group_id', 'rule_id', 'bid', 'bid_processed', 'status_id', 'created_at'];
        }

        return parent::attributes();
    }

    public function getStatus()
    {
        return ScheduleStatusEnum::findAll()[$this->status_id] ?? null;
    }

    /**
     * @param int $dateRange
     * @return ActiveQuery
     */
    public function createQuery(int $dateRange) : ActiveQuery
    {
        return self::find()
            ->select([
                'MAX(' . self::tableName() . '.autoincrement_id) autoincrement_id',
                self::tableName() . '.ad_group_id',
                self::tableName() . '.bid',
                self::tableName() . '.bid_processed',
                Campaign::tableName() . '.api_token_id'
            ])
            ->where(['>=', self::tableName() . '.created_at', $this->createDatePeriod($dateRange)])
            ->innerJoin(Bid::tableName(), Bid::tableName() . '.ad_group_id = ' . self::tableName() . '.ad_group_id')
            ->innerJoin(Campaign::tableName(), Campaign::tableName() . '.campaign_id = ' . Bid::tableName() . '.campaign_id')
            ->groupBy([self::tableName() . '.ad_group_id'])
            ->orderBy([
                Campaign::tableName() . '.api_token_id' => SORT_DESC
            ]);
    }

    public function getBids()
    {
        return $this->hasOne(Bid::class, ['ad_group_id' => 'ad_group_id']);
    }

    public function search($params)
    {
        $query = self::find()->select(['*', 'date(created_at) created_at']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'rule_id' => SORT_ASC,
                    'status_id' => SORT_DESC,
                    'created_at' => SORT_DESC,
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ad_group_id' => $this->ad_group_id,
            'rule_id' => $this->rule_id,
            'status_id' => $this->status_id,
        ]);

        if ($this->created_at) {
            $query->andWhere([
                '=', 'date(created_at)', $this->created_at
            ]);
        }
        

        return $dataProvider;
    }

}
