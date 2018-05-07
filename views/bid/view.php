<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Bid */

$this->title = $model->autoincrement_id;
$this->params['breadcrumbs'][] = ['label' => 'Bids', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bid-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Checked Yandex', ['checked', 'id' => $model->ad_group_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to sent request?',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'autoincrement_id',
            'ad_group_id',
            'campaign_id',
            'bid',
            'context_bid',
            'min_search_price',
            'current_search_price',
            'updated_at',
        ],
    ]) ?>

</div>
