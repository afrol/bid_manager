<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ApiTokenSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="api-token-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'api_token_id') ?>

    <?= $form->field($model, 'api_service_id') ?>

    <?= $form->field($model, 'token') ?>

    <?= $form->field($model, 'account_login') ?>

    <?= $form->field($model, 'status_id') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
