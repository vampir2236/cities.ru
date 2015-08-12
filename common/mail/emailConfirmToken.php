<?php

use yii\helpers\Html;

$emailconfirmLink = Yii::$app->urlManager->createAbsoluteUrl(
    ['site/confirm-email', 'token' => $user->email_confirm_token]);
?>

<div class="password-reset">
    <p>Здравствуйте, <?= Html::encode($user->fio) ?>,</p>

    <p>Для подтверждения Вашего email перейдите по ссылке, указанной ниже</p>

    <p><?= Html::a(Html::encode($emailconfirmLink), $emailconfirmLink) ?></p>
</div>
