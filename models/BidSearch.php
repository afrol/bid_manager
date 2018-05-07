<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Bid;

/**
 * BidSearch represents the model behind the search form of `app\models\Bid`.
 */
class BidSearch extends Bid
{

    public $tokenId;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['autoincrement_id', 'ad_group_id', 'campaign_id', 'bid', 'context_bid', 'min_search_price', 'current_search_price'], 'integer'],
            [['updated_at'], 'safe'],
            [['tokenId'], 'safe']
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
        $query = Bid::find();

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
            'bid' => $this->bid,
            'context_bid' => $this->context_bid,
            'min_search_price' => $this->min_search_price,
            'current_search_price' => $this->current_search_price,
            'updated_at' => $this->updated_at,
        ]);

        return $dataProvider;
    }
}
