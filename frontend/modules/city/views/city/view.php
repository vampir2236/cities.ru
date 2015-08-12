<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->name;
?>

<div class="city-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
        ],
    ]) ?>
    <hr>

    <div>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить город?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::button('Закрыть', [
            'class' => 'btn btn-default',
            'data-dismiss' => 'modal',
        ]); ?>
    </div>

</div>
