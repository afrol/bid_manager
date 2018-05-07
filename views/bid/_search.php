<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\BidSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bid-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'autoincrement_id') ?>

    <?= $form->field($model, 'ad_group_id') ?>

    <?= $form->field($model, 'campaign_id') ?>

    <?= $form->field($model, 'bid') ?>

    <?= $form->field($model, 'context_bid') ?>

    <?php // echo $form->field($model, 'min_search_price') ?>

    <?php // echo $form->field($model, 'current_search_price') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
