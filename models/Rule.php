<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "rules".
 *
 * @property int $rules_id
 * @property string $name
 * @property string $rule
 * @property string $value
 * @property string $description
 * @property int $status_id
 * @property int $position
 * @property string $updated_at
 */
class Rule extends ActiveRecord
{

    private static $allowFields = [

    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rules';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['updated_at'], 'safe'],
            [['name'], 'string', 'max' => 100],
            [['rule'], 'string'],
            [['value'], 'string'],
            [['description'], 'string', 'max' => 255],
            [['status_id', 'position'], 'string', 'max' => 4],
            [['status_id'], 'default', 'value'=>'1'],
            [['position'], 'default', 'value'=>'0'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rule_id' => 'Rules ID',
            'name' => 'Name',
            'rule' => 'Rule',
            'value' => 'Value',
            'description' => 'Description',
            'status_id' => 'Status',
            'position' => 'Position',
            'updated_at' => 'Updated At',
        ];
    }

    public function behaviors()
    {
        return [

        ];
    }

    public function getRule()
    {
        return self::find()
            ->select('rule')
            ->where(['status_id' => 1])
            ->orderBy(['position' => SORT_DESC])
            ->asArray()
            ->column();
    }


    public function applyRule(ActiveQuery $query) : array
    {
        $conditions = $this->getConditions();

        $queryRules = [];

        foreach ($conditions as $ruleId => $condition) {
            $queryCondition = clone $query;
            if (isset($condition['where'])) {
                array_map(function ($a) use ($queryCondition) {
                    $queryCondition->andWhere($a);
                }, $condition['where']);
            }

            if (isset($condition['having'])) {
                array_map(function ($a) use ($queryCondition) {
                    $queryCondition->andHaving($a);
                }, $condition['having']);
            }

            $queryRules[$ruleId] = $queryCondition;
        }

        return $queryRules;
    }

    public function getConditions()
    {
        return RuleCondition::find();
    }
}
