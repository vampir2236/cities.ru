<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Добавить отзыв';
?>

<div class="review-form padding">

    <h2>Выбор города</h2>
    <hr>

    <?php if (!empty($currentCity)): ?>
        <div class="city-by-ip-form">
            <p class="review-subtitle text-center"> <?= $currentCity; ?> Ваш город? </p>
            <hr>
            <?= Html::a('Да', ['choose-city', 'city' => $currentCity], [
                'class' => 'btn btn-primary',
            ]) ?>
            <?= Html::button('Нет', [
                'class' => 'btn btn-default',
                'id' => 'show-city-list-form',
            ]) ?>
        </div>
    <?php endif; ?>

    <div class="city-list-form" <?= !empty($currentCity) ? 'style="display: none"' : '' ?>>
        <div class="list-group">
            <?php foreach ($cities as $city) {
                echo Html::a($city, ['choose-city', 'city' => $city], [
                    'class' => 'list-group-item',
                ]);
            } ?>
        </div>
    </div>
</div>

<?php $this->registerJs('citiesModule.reinit();'); ?>