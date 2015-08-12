<?php

use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

?>

<div class="city-form">

    <?php $form = ActiveForm::begin([
        'id' => 'city-form',
    ]); ?>

    <hr>
    <?= $form->field($model, 'name')->widget(Select2::className(), [
        'size' => Select2::MEDIUM,
        'language' => 'ru',
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 3,
            'ajax' => [
                'url' => Url::to(['/city/city/city-list']),
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
        <?= Html::button('Закрыть', [
            'class' => 'btn btn-default',
            'data-dismiss' => 'modal',
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php $this->registerJs('citiesModule.reinit();'); ?>