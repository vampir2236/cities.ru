<?php

use kartik\rating\StarRating;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;


if (isset($author)) {
    $this->params['breadcrumbs'][] = $author->fio;
    $this->title = $author->fio;
} else {
    $this->title = !empty($city) ? $city : 'Отзывы';
}

?>

<div class="review-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <hr>


    <?php if (!Yii::$app->user->isGuest) {
        echo '<p>' . Html::a('Добавить отзыв', ['/review/create'], [
                    'class' => 'btn btn-success',
                    'data-dismiss' => 'modal',
                    'data-target' => '#modal-form',
                    'data-toggle' => 'modal',
                ]
            ) . '</p>';
    } ?>

    <div class="container">
        <div class="row">
            <div class="col-md-10 ">

                <?php
                Pjax::begin([
                    'id' => 'refreshReviews',
                    'timeout' => 5000,
                    'linkSelector' => false,
                    'formSelector' => false,
                ]);
                if (!isset($dataProvider) || $dataProvider->totalCount === 0) {
                    echo '<p>По выбранному городу нет отзывов</p>';
                }
                ?>

                <?php if (isset($dataProvider)) {
                    foreach ($dataProvider->models as $review): ?>
                        <div class="review-preview">
                            <a href="<?= Url::to(['/review/view', 'id' => $review->id]); ?>">
                                <h2 class="review-title">
                                    <?= $review->title; ?>
                                </h2>
                            </a>

                            <?= StarRating::widget([
                                'id' => 'rating' . $review->id,
                                'name' => 'rating',
                                'value' => $review->rating,
                                'pluginOptions' => [
                                    'readonly' => true,
                                    'showClear' => false,
                                    'showCaption' => false,
                                    'stars' => 5,
                                    'size' => 'xs',
                                ],
                            ]); ?>

                            <h3 class="review-subtitle">
                                <?= StringHelper::truncateWords($review->text, 30); ?>
                            </h3>

                            <?php if (isset($author)):
                                $cities = implode(', ', ArrayHelper::getColumn(
                                    $review->cities, 'name'));
                                ?>
                                <p class="review-meta">
                                    <?= empty($cities) ? 'Для всех городов России' : $cities; ?>
                                </p>
                            <?php endif; ?>

                            <p class="review-meta">
                                <?php if (!Yii::$app->user->isGuest) {
                                    echo Html::a($review->author->fio, [
                                        '/review/author', 'id' => $review->id_author], [
                                        'data-dismiss' => 'modal',
                                        'data-target' => '#modal-form',
                                        'data-toggle' => 'modal',
                                    ]);
                                } else {
                                    echo $review->author->fio;
                                } ?>
                                <?= date('d.m.Y H:i', $review->created_at); ?>
                            </p>
                        </div>
                        <hr>
                    <?php endforeach; ?>

                    <?= LinkPager::widget([
                        'pagination' => $dataProvider->pagination,
                        'nextPageLabel' => 'Предыдущие &rarr;',
                        'prevPageLabel' => '&larr; Следующие',
                        'disabledPageCssClass' => 'hidden',
                        'maxButtonCount' => 0,
                        'options' => [
                            'class' => 'pager pull-right',
                        ]
                    ]); ?>

                <?php }
                Pjax::end(); ?>
            </div>
        </div>
    </div>
</div>


<?php
// если сессия не валидна принудительно открываем окно выбора города
if (!$isValidSession) {
    $this->registerJs('citiesModule.chooseCity();', $this::POS_END);
}
?>



