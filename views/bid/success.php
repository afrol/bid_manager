<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $grepRows integer */

$this->title = 'Bid list';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bid-index">

    <div class="jumbotron">
        <h1>Congratulations!</h1>

        <p class="lead">You have successfully grep new <?= Html::encode($this->title)?> amount of <?=Html::encode($grepRows)?>.</p>

        <p><?= Html::a('View Bid list', ['index'], ['class' => 'btn btn-lg btn-success']) ?></p>
    </div>

</div>
