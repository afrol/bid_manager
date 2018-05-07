<?php

namespace app\components\Dwh;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\components\Dwh\Statistic;

/**
 * StatisticSearch represents the model behind the search form of `app\components\Dwh\Statistic`.
 */
class StatisticSearch extends Statistic
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date'], 'safe'],
            [['data_source_id', 'project_id', 'site_id', 'campaign_id', 'ad_group_id', 'visits_corrected', 'ppc_clicks', 'avg_cpc'], 'integer'],
            [['ppc_expenses_usd', 'revenue_total_usd', 'avg_click_position'], 'number'],
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
        $query = Statistic::find();

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
            'date' => $this->date,
            'data_source_id' => $this->data_source_id,
            'project_id' => $this->project_id,
            'site_id' => $this->site_id,
            'campaign_id' => $this->campaign_id,
            'ad_group_id' => $this->ad_group_id,
            'visits_corrected' => $this->visits_corrected,
            'ppc_clicks' => $this->ppc_clicks,
            'ppc_expenses_usd' => $this->ppc_expenses_usd,
            'revenue_total_usd' => $this->revenue_total_usd,
            'avg_click_position' => $this->avg_click_position,
            'avg_cpc' => $this->avg_cpc,
        ]);

        return $dataProvider;
    }
}
