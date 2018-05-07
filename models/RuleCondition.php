<?php

namespace app\models;

use app\models\Enum\ScheduleStatusEnum;
use yii\base\Model;

class RuleCondition extends Model
{

    const FIELD_ROI = 'AVG(revenue_total_usd/ppc_expenses_usd - 1)';
    const FIELD_POSITION = 'AVG(avg_click_position)';
    const FIELD_CLICKS = 'AVG(ppc_clicks)';
    const FIELD_BID = 'bid';
    const FIELD_AVG_CPC = 'avg_cpc';

    const ADD = '+';
    const SUB = '-';
    const EQUAL = '=';

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $filed;

    /**
     * @var string
     */
    public $condition;

    /**
     * @var string
     */
    public $var;

    private static $availableField = [
        self::FIELD_ROI,
        self::FIELD_POSITION,
        self::FIELD_CLICKS
    ];

    private static $conditionList = [
        1 => [
            'having' => [
                ['>=', self::FIELD_POSITION, 2],
                ['<=', self::FIELD_POSITION, 2.99],
                ['>=', self::FIELD_ROI, 0.45],
                ['<=', self::FIELD_ROI, 0.7],
            ],
            'value' => false,
        ],
        2 => [
            'having' => [
                ['>=', self::FIELD_POSITION, 3],
                ['<=', self::FIELD_POSITION, 3.99],
                ['>=', self::FIELD_ROI, 0.45],
                ['<=', self::FIELD_ROI, 0.7],
            ],
            'value' => [self::ADD, self::FIELD_BID, 15],
        ],
        3 => [
            'having' => [
                ['>=', self::FIELD_POSITION, 4],
                ['<=', self::FIELD_POSITION, 4.99],
                ['>=', self::FIELD_ROI, 0.45],
                ['<=', self::FIELD_ROI, 0.7],
            ],
            'value' => [self::ADD, self::FIELD_BID, 20],
        ],
        4 => [
            'having' => [
                ['>=', self::FIELD_POSITION, 5],
                ['<=', self::FIELD_POSITION, 5.99],
                ['>=', self::FIELD_ROI, 0.45],
                ['<=', self::FIELD_ROI, 0.7],
            ],
            'value' => [self::ADD, self::FIELD_BID, 30],
        ],
        5 => [
            'having' => [
                ['>=', self::FIELD_POSITION, 6],
                ['>=', self::FIELD_ROI, 0.45],
                ['<=', self::FIELD_ROI, 0.7],
            ],
            'value' => [self::ADD, self::FIELD_BID, 40],
        ],
        6 => [
            'having' => [
                ['>=', self::FIELD_POSITION, 2],
                ['<=', self::FIELD_POSITION, 2.99],
                ['>', self::FIELD_ROI, 0.7],
            ],
            'value' => [self::ADD, self::FIELD_BID, 15],
        ],
        7 => [
            'having' => [
                ['>=', self::FIELD_POSITION, 3],
                ['<=', self::FIELD_POSITION, 3.99],
                ['>', self::FIELD_ROI, 0.7],
            ],
            'value' => [self::ADD, self::FIELD_BID, 25],
        ],
        8 => [
            'having' => [
                ['>=', self::FIELD_POSITION, 4],
                ['<=', self::FIELD_POSITION, 4.99],
                ['>', self::FIELD_ROI, 0.7],
            ],
            'value' => [self::ADD, self::FIELD_BID, 30],
        ],
        9 => [
            'having' => [
                ['>=', self::FIELD_POSITION, 5],
                ['<=', self::FIELD_POSITION, 5.99],
                ['>', self::FIELD_ROI, 0.7],
            ],
            'value' => [self::ADD, self::FIELD_BID, 40],
        ],
        10 => [
            'having' => [
                ['>=', self::FIELD_POSITION, 6],
                ['>', self::FIELD_ROI, 0.7],
            ],
            'value' => [self::ADD, self::FIELD_BID, 50],
        ],
        11 => [
            'having' => [
                ['>', self::FIELD_CLICKS, 500],
                ['>=', self::FIELD_ROI, -0.1],
                ['<=', self::FIELD_ROI, 0.15],
            ],
            'value' => false,
        ],
        12 => [
            'having' => [
                ['<', self::FIELD_CLICKS, 500],
                ['>=', self::FIELD_ROI, -0.1],
                ['<=', self::FIELD_ROI, 0.15],
            ],
            'value' => [self::EQUAL, self::FIELD_AVG_CPC, 0],
        ],
        13 => [
            'having' => [
                ['>=', self::FIELD_ROI, -0.1],
                ['<=', self::FIELD_ROI, -0.3],
            ],
            'value' => [self::SUB, self::FIELD_AVG_CPC, 10],
        ],
        14 => [
            'having' => [
                ['<=', self::FIELD_ROI, -0.31],
            ],
            'value' => [self::SUB, self::FIELD_AVG_CPC, 20],
        ],
    ];

    public function save()
    {
        return serialize(self::$conditionList);
    }

    public function create($data)
    {
        self::$conditionList = unserialize($data);
        $this->load(self::$conditionList);
    }

    public static function find()
    {
        return self::$conditionList;
    }

    public static function findById($id)
    {
        return self::$conditionList[$id] ?? null;
    }
    
    public function getValueByRuleId(int $ruleId)
    {
        return self::findById($ruleId)['value'];
    }

    public function getNewBidAmount($item, $value)
    {
        list($operator, $field, $var) = $value;

        $amount = $item[$field];
        if (!$amount || $amount <= 0) {
            return false;
        }

        $percentAmount = $var ? $amount * $var / 100 : 0;

        switch ($operator) {
            case self::ADD:
                $amount = $amount + $percentAmount;
                break;
            case self::SUB:
                $amount = $amount - $percentAmount;
                break;
            case self::EQUAL:
                break;
            default:
                return false;
        }

        return $amount;
    }
}
