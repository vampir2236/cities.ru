<?php

use yii\helpers\Html;


$this->title = 'Автор';
?>


<div class="author">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr>

    <div class="author-info">
        <div class="row">
            <div class="col-xs-3">
                <p class="author-info__label">ФИО</p>
            </div>
            <div class="col-xs-9">
                <p><?= $model->fio; ?></p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <p class="author-info__label">Email</p>
            </div>
            <div class="col-xs-9">
                <p><?= Html::a($model->email, $model->emailLink); ?></p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <p class="author-info__label">Телефон</p>
            </div>
            <div class="col-xs-9">
                <p><?= Html::a($model->phone, $model->phoneLink); ?></p>
            </div>
        </div>
    </div>

    <hr>
    <?= Html::a('Посмотреть все отзывы',
        ['/review/review/index', 'id_author' => $model->id], [
            'class' => 'btn btn-primary',
        ]); ?>
    <?= Html::button('Закрыть', [
        'class' => 'btn btn-default',
        'data-dismiss' => 'modal',
    ]); ?>

</div>
