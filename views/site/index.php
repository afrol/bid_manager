<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Bid manager';
?>
<div class="site-index">

    <div class="body-content">

        <div class="row">
            <h2>List service</h2>
            <ul class="list-group">
                <li class="list-group-item"><?= Html::a('Bids', ['/bid']) ?></li>
                <li class="list-group-item"><?= Html::a('Schedule', ['/bid-schedule']) ?></li>
                <li class="list-group-item"><?= Html::a('Rules', ['/rule']) ?></li>
                <li class="list-group-item"><?= Html::a('Schedule sql rule', ['/bid-schedule/view-sql']) ?></li>
                <li class="list-group-item"><?= Html::a('Token', ['/token']) ?></li>
                <li class="list-group-item"><?= Html::a('Log bids', ['/log-bid']) ?></li>
            </ul>
        </div>

    </div>
</div>
