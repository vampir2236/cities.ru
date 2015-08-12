<?php
use yii\captcha\Captcha;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\MaskedInput;

$this->title = 'Регистрация';
?>

<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>
    <hr>

    <div class="row">
        <div class="col-xs-12">
            <?php $form = ActiveForm::begin([
                'id' => 'signup-form',
            ]); ?>
            <?= $form->field($model, 'email')->textInput(['type' => 'email']); ?>
            <?= $form->field($model, 'fio'); ?>
            <?= $form->field($model, 'phone')->widget(MaskedInput::className(), [
                'mask' => '+7 (999) 999-99-99',
            ]); ?>
            <?= $form->field($model, 'password')->passwordInput() ?>
            <?= $form->field($model, 'verifyCode')->widget(Captcha::className()) ?>

            <hr>
            <div class="form-group">
                <?= Html::submitButton('Зарегистрироваться', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                <?= Html::a('Закрыть', '#', [
                    'data-dismiss' => 'modal',
                    'class' => 'btn btn-default',
                ]) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php $this->registerJs('citiesModule.reinit();'); ?>