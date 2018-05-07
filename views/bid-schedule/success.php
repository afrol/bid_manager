<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $grepRows integer */

$this->title = 'Schedule list';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bid-index">

    <div class="jumbotron">
        <h1>Congratulations!</h1>

        <p class="lead">You have successfully new <?= Html::encode($this->title)?> amount of <?=Html::encode($grepRows)?>.</p>

        <p><?= Html::a('View list', ['index'], ['class' => 'btn btn-lg btn-success']) ?></p>
    </div>

</div>
