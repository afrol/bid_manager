<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Campaign;
use yii\data\Pagination;
use yii\db\ActiveQuery;

/**
 * CampaignSearch represents the model behind the search form of `app\models\Campaign`.
 */
class CampaignSearch extends Campaign
{

    const LIMIT_GROUPS = 10;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['autoincrement_id', 'campaign_id', 'api_token_id'], 'integer'],
            [['name', 'type_id', 'state_id', 'updated_at'], 'safe'],
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
        $query = Campaign::find();

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
            'campaign_id' => $this->campaign_id,
            'api_token_id' => $this->api_token_id,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'type_id', $this->type_id])
            ->andFilterWhere(['like', 'state_id', $this->state_id]);

        return $dataProvider;
    }

    public static function findByTokenId1($tokenId, $page = 0)
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

    /**
     * @param $tokenId
     * @param int $page
     * @return ActiveQuery
     */
    public static function findByTokenId($tokenId, $page = 0)
    {
        return self::find()
            ->select('campaign_id')
            ->where([
                'state_id' => CampaignState::STATE_ON,
                'api_token_id' => $tokenId
            ])
            ->limit(self::LIMIT_GROUPS)
            ->offset($page * self::LIMIT_GROUPS);
    }
}
