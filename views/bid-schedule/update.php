<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\BidSchedule */

$this->title = 'Update Bid Schedule: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Bid Schedules', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->autoincrement_id, 'url' => ['view', 'id' => $model->autoincrement_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="bid-schedule-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
