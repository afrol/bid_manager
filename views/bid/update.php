<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Bid */

$this->title = 'Update Bid: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Bids', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ad_group_id, 'url' => ['view', 'id' => $model->ad_group_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="bid-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>