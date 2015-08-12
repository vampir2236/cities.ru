<?php

use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;

$this->title = 'Города';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="city-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr>

    <?php
    if (!Yii::$app->user->isGuest) {
        echo '<p>' . Html::a('Добавить город', ['/city/create'], [
                'class' => 'btn btn-success',
                'data-dismiss' => 'modal',
                'data-target' => '#modal-form',
                'data-toggle' => 'modal',
            ]) . '</p>';
    }
    ?>

    <div class="container">
        <div class="row">
            <div class="col-md-10 ">

                <?php
                Pjax::begin(['id' => 'cities']);
                ?>

                <?php if ($dataProvider->totalCount === 0): ?>
                    <p>В БД нет городов</p>
                <?php endif; ?>
                <div class="list-group">
                    <?php foreach ($dataProvider->models as $city): ?>
                        <?= Html::a($city->name, ['/city/view', 'id' => $city->id],
                            [
                                'data-dismiss' => 'modal',
                                'data-target' => '#modal-form',
                                'data-toggle' => 'modal',
                                'class' => 'list-group-item',
                            ]); ?>
                    <?php endforeach; ?>
                </div>

                <?= LinkPager::widget([
                    'pagination' => $dataProvider->pagination,
                    'nextPageLabel' => 'Следующие &rarr;',
                    'prevPageLabel' => '&larr; Предыдущие',
                    'disabledPageCssClass' => 'hidden',
                    'maxButtonCount' => 0,
                    'options' => [
                        'class' => 'pager pull-right',
                    ]
                ]); ?>

                <?php
                Pjax::end();
                ?>
            </div>
        </div>
    </div>
</div>
