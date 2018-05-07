<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\BidSchedule */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bid-schedule-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ad_group_id')->textInput() ?>

    <?= $form->field($model, 'rule_id')->textInput() ?>

    <?= $form->field($model, 'bid')->textInput() ?>

    <?= $form->field($model, 'bid_processed')->textInput() ?>

    <?= $form->field($model, 'status_id')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
