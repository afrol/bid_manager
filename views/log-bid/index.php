<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Log Bids';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="log-bid-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'prefix',
            'log_time:datetime',
            [
                'label' => 'Details',
                'format' => 'ntext',
                'value' => function($data) {
                    return print_r(unserialize($data->message), true);
                },
            ]
        ],
    ]); ?>
</div>
