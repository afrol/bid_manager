<?php

namespace app\components\Dwh;

use app\components\Behaviors\DateBehavior;
use app\models\Bid;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "Pcu_bid_manager_yandex_direct".
 *
 * @property string $date
 * @property int $data_source_id
 * @property int $project_id
 * @property int $site_id
 * @property int $campaign_id
 * @property int $ad_group_id
 * @property int $visits_corrected
 * @property int $ppc_clicks
 * @property string $ppc_expenses_usd
 * @property string $revenue_total_usd
 * @property string $avg_click_position
 * @property int $avg_cpc
 *
 *
 * DateBehavior
 * @method Statistic createDatePeriod(int $day) : string
 */

class Statistic extends SourceAbstract
{

    const FIELD_AVG_ROI = 'AVG(revenue_total_usd/ppc_expenses_usd - 1) roi';
    const FIELD_AVG_POSITION = 'AVG(avg_click_position) position';
    const FIELD_AVG_CLICKS = 'AVG(ppc_clicks) clicks';
    const FIELD_AVG_CPC = 'AVG(avg_cpc) avg_cpc';
    const FIELD_BID = 'bid';
    const FIELD_CPC = 'avg_cpc';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Pcu_bid_manager_yandex_direct';
    }

    /**
     * @var float
     */
    private $roi;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date', 'data_source_id', 'project_id', 'site_id', 'campaign_id', 'ad_group_id', 'visits_corrected', 'ppc_clicks', 'ppc_expenses_usd', 'revenue_total_usd'], 'required'],
            [['date'], 'safe'],
            [['data_source_id', 'project_id', 'site_id', 'campaign_id', 'ad_group_id', 'visits_corrected', 'ppc_clicks', 'avg_cpc'], 'integer'],
            [['ppc_expenses_usd', 'revenue_total_usd', 'avg_click_position'], 'number'],
            [['ad_group_id', 'campaign_id', 'data_source_id', 'date', 'project_id', 'site_id'], 'unique', 'targetAttribute' => ['ad_group_id', 'campaign_id', 'data_source_id', 'date', 'project_id', 'site_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'date' => 'Date',
            'data_source_id' => 'Data Source ID',
            'project_id' => 'Project ID',
            'site_id' => 'Site ID',
            'campaign_id' => 'Campaign ID',
            'ad_group_id' => 'Ad Group ID',
            'visits_corrected' => 'Visits Corrected',
            'ppc_clicks' => 'Ppc Clicks',
            'ppc_expenses_usd' => 'Ppc Expenses Usd',
            'revenue_total_usd' => 'Revenue Total Usd',
            'avg_click_position' => 'Avg Click Position',
            'avg_cpc' => 'Avg Cpc',
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => DateBehavior::class,
                'time' => function () {
                    return self::find()
                        ->select('date')
                        ->orderBy(['date' => SORT_DESC])
                        ->limit(1)
                        ->scalar();
                },
            ],
        ];
    }

    /**
     * @return float
     */
    public function getRoi(): float
    {
        if ($this->roi !== null) {
            return $this->roi;
        }

        if (empty($this->revenue_total_usd) || empty($this->ppc_expenses_usd)) {
            return 0;
        }

        if ($this->roi === null) {
            $this->setRoi(
                $this->revenue_total_usd / $this->ppc_expenses_usd - 1
            );
        }

        return $this->roi;
    }

    /**
     * @param float $roi
     */
    public function setRoi(float $roi = 0): void
    {
        $this->roi = $roi;
    }

    /* ActiveRelation */
    public function getBid()
    {
        return $this->hasOne(Bid::class, ['ad_group_id' => 'ad_group_id']);
    }

    /**
     * @param int $dateRange
     * @return ActiveQuery
     */
    public function createQuery(int $dateRange) : ActiveQuery
    {
        return self::find()
            ->select(['ad_group_id', self::FIELD_AVG_CPC, self::FIELD_AVG_ROI, self::FIELD_AVG_POSITION, self::FIELD_AVG_CLICKS])
            ->where(['>', 'date', $this->createDatePeriod($dateRange)])
            ->andWhere(['>', 'ad_group_id', 0])
            ->andWhere(['>', 'revenue_total_usd', 0])
            ->andWhere(['>', 'ppc_expenses_usd', 0])
            ->groupBy('ad_group_id')
            ->with('bid')
            ->asArray();
    }
}
