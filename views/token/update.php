<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ApiToken */

$this->title = 'Update Api Token: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Api Tokens', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->api_token_id, 'url' => ['view', 'id' => $model->api_token_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="api-token-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
