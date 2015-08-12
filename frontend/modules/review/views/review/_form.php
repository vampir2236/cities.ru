<?php

use kartik\file\FileInput;
use kartik\widgets\Select2;
use kartik\widgets\StarRating;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

?>

<div class="review-form">

    <?php $form = ActiveForm::begin([
        'options' => [
            'enctype' => 'multipart/form-data',
            'id' => 'review-form',
        ],
    ]); ?>

    <hr>
    <?= $form->field($review, 'title')->textInput(['maxlength' => true]) ?>
    <?= $form->field($review, 'text')->textarea(['rows' => 5]) ?>

    <?= $form->field($review, 'rating')->widget(StarRating::classname(), [
        'pluginOptions' => [
            'stars' => 5,
            'min' => 0,
            'max' => 5,
            'step' => 1,
            'showCaption' => false,
            'showClear' => false,
            'size' => 'xs',
        ],
    ]); ?>

    <?= $form->field($review, 'image')->widget(FileInput::className(), [
        'options' => [
            'accept' => 'image/*',
        ],
        'pluginOptions' => [
            'language' => 'ru',
            'initialPreview' => isset($imagePreview) ? $imagePreview['initialPreview'] : [],
            'initialPreviewConfig' => isset($imagePreview) ? $imagePreview['initialPreviewConfig'] : [],
            'showUpload' => false,
            'showRemove' => false,
            'showCaption' => true,
        ],
    ]); ?>

    <?= $form->field($review, 'ids_city')->widget(Select2::className(), [
        'data' => $cities,
        'size' => Select2::MEDIUM,
        'language' => 'ru',
        'options' => [
            'multiple' => true,
        ],
        'pluginOptions' => [
            'allowClear' => true,
        ],
        'addon' => [
            'append' => [
                'content' => Html::button('Добавить', [
                    'class' => 'btn btn-success toggle-add-city-form',
                ]),
                'asButton' => true,
            ]
        ],
        'id' => 'ids-city',
    ]); ?>

    <hr>
    <div class="form-group">
        <?= Html::submitButton($review->isNewRecord ? 'Создать' : 'Редактировать',
            ['class' => $review->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::button('Закрыть', [
            'class' => 'btn btn-default',
            'data-dismiss' => 'modal',
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<div class="add-city-form" style="display: none">
    <?php $form = ActiveForm::begin([
        'id' => 'add-city-form',
        'action' => '/review/city-create',
    ]); ?>

    <hr>
    <?= $form->field($createdCity, 'name')->widget(Select2::className(), [
        'size' => Select2::MEDIUM,
        'language' => 'ru',
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 3,
            'ajax' => [
                'url' => Url::to(['/city/city-list']),
                'dataType' => 'json',
                'data' => new JsExpression('function(params) { return {q:params.term}; }'),
            ],
            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
            'templateResult' => new JsExpression('function(city) { return city.text; }'),
            'templateSelection' => new JsExpression('function (city) { return city.text; }'),
        ],
    ]);
    ?>

    <hr>
    <div class="form-group">
        <?= Html::submitButton('Создать', ['class' => 'btn btn-success']) ?>
        <?= Html::button('Отмена', [
            'class' => 'btn btn-default toggle-add-city-form',
        ]) ?>
    </div>

    <?php ActiveForm::end() ?>
</div>

<?php $this->registerJs('citiesModule.reinit();'); ?>