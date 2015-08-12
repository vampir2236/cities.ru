<?php

use yii\helpers\Html;

$this->title = 'Редактирование отзыва';

?>

<div class="review-update padding">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'review' => $review,
        'cities' => $cities,
        'imagePreview' => $imagePreview,
        'createdCity' => $createdCity,
    ]) ?>

</div>
