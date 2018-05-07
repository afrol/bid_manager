<?php

namespace app\models;

use app\models\Enum\StatusEnum;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AdGroup;
use yii\db\ActiveQuery;
use yii\db\QueryBuilder;

/**
 * AdGroupSearch represents the model behind the search form of `app\models\AdGroup`.
 */
class AdGroupSearch extends AdGroup
{

    public $type;

    public $status;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['autoincrement_id', 'ad_group_id', 'campaign_id'], 'integer'],
            [['name', 'type_id', 'status_id', 'updated_at'], 'safe'],
            [['type'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = AdGroup::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'autoincrement_id' => $this->autoincrement_id,
            'ad_group_id' => $this->ad_group_id,
            'campaign_id' => $this->campaign_id,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'type_id', $this->type_id])
            ->andFilterWhere(['like', 'status_id', $this->status_id]);

        return $dataProvider;
    }

    /**
     * @param $tokenId
     * @param int $page
     * @return ActiveQuery
     */
    public static function findByTokenId($tokenId, $page = 0)
    {
        return self::find()
            ->select('ad_group_id')
            ->innerJoinWith('campaign')
            ->where([
                'status_id' => StatusEnum::STATUS_ACCEPTED,
                'api_token_id' => $tokenId
            ])
            ->limit(self::LIMIT_GROUPS)
            ->offset($page * self::LIMIT_GROUPS);
    }
}
