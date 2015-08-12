<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = 'Войти';
?>

<div class="site-login">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
    ]); ?>
    <?= $form->field($loginForm, 'email')->textInput([
        'type' => 'email',
    ]) ?>
    <?= $form->field($loginForm, 'password')->passwordInput() ?>
    <?= $form->field($loginForm, 'rememberMe')->checkbox() ?>
    <?= Html::button('забыли пароль?', [
        'id' => 'show-reset-password-form',
        'class' => 'btn btn-link pu1ll-right',
    ]); ?>

    <hr>
    <div class="form-group">
        <?= Html::submitButton('Войти', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        <?= Html::a('Закрыть', '#', [
            'data-dismiss' => 'modal',
            'class' => 'btn btn-default',
        ]) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>


<div class="site-request-password-reset" style="display: none">
    <h1><?= 'Восстановление пароля' ?></h1>
    <hr>

    <p>Введите Ваш email, на который будет отправлено письмо c инструкцией по восстановлению пароля.</p>

    <?php $form = ActiveForm::begin([
        'id' => 'request-password-reset-form',
        'action' => Url::to(['/site/request-password-reset']),
    ]); ?>
    <?= $form->field($passwordResetRequestForm, 'email')
        ->textInput(['type' => 'email']) ?>

    <hr>
    <div class="form-group">
        <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']); ?>
        <?= Html::a('Закрыть', '#', [
            'data-dismiss' => 'modal',
            'class' => 'btn btn-default',
        ]) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?php $this->registerJs('citiesModule.reinit();'); ?>