<?php

use yii\helpers\Html;

$this->title = 'Добавить отзыв';
?>

<div class="review-create padding">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'review' => $review,
        'cities' => $cities,
        'createdCity' => $createdCity,
    ]) ?>
</div>
