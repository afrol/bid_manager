<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\ApiToken */

$this->title = 'Create Api Token';
$this->params['breadcrumbs'][] = ['label' => 'Api Tokens', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="api-token-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
