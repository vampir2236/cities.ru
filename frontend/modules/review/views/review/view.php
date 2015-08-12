<?php

use kartik\widgets\StarRating;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\widgets\Pjax;

$this->title = $model->title;
$this->params['breadcrumbs'][] = StringHelper::truncate($this->title, 25);
// пользователь автор отзыва?
$isReviewAuthor = Yii::$app->user->id == $model->id_author;

?>

<?php Pjax::begin([
    'id' => 'refreshReview',
    'timeout' => 5000,
    'formSelector' => false,
    'linkSelector' => false,
]); ?>

<div class="review-view">
    <article>
        <div class="container">

            <h1><?= Html::encode($this->title) ?></h1>
            <hr>

            <p>
                <?php if ($isReviewAuthor) {
                    echo Html::a('Редактирование',
                        ['review/update', 'id' => $model->id],
                        [
                            'class' => 'btn btn-primary',
                            'data-dismiss' => 'modal',
                            'data-target' => '#modal-form',
                            'data-toggle' => 'modal',
                        ]);
                } ?>
                <?php if ($isReviewAuthor) {
                    echo Html::a('Удаление', ['review/delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Вы действительно хотите удалить отзыв?',
                            'method' => 'post',
                        ],
                    ]);
                } ?>
            </p>

            <div class="row">
                <div class="col-md-10">

                    <?php if ($model->img) {
                        echo Html::img(
                            Yii::$app->params['uploadUrl'] . $model->img,
                            [
                                'class' => 'img-responsive',
                                'alt' => $model->title,
                            ]);
                    } ?>

                    <?= StarRating::widget([
                        'name' => 'rating',
                        'value' => $model->rating,
                        'pluginOptions' => [
                            'readonly' => true,
                            'showClear' => false,
                            'showCaption' => false,
                            'stars' => 5,
                            'size' => 'xs',
                        ],
                    ]); ?>
                    <p><?= $model->text; ?></p>

                    <p class="post-meta">
                        <?php if (!Yii::$app->user->isGuest) {
                            echo Html::a($model->author->fio, [
                                'review/author', 'id' => $model->id_author,
                            ], [
                                'data-dismiss' => 'modal',
                                'data-target' => '#modal-form',
                                'data-toggle' => 'modal',
                            ]);
                        } else {
                            echo $model->author->fio;
                        } ?>

                        <?= date('d.m.Y H:i', $model->created_at); ?>
                    </p>
                </div>
            </div>

        </div>
    </article>

</div>

<?php Pjax::end(); ?>