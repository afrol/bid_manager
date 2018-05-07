<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Bid Schedules';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bid-schedule-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'filterModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'ad_group_id',
            'rule_id',
            'bid',
            'bid_processed',
            'Status',
            'created_at',
            'updated_at',
        ],
    ]); ?>
</div>
