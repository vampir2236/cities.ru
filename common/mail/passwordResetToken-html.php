<?php

use yii\helpers\Html;

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(
    ['site/reset-password', 'token' => $user->password_reset_token]);

?>

<div class="password-reset">
    <p>Здравстуйте, <?= Html::encode($user->fio) ?>,</p>

    <p>Для восстановления пароля перейдите по ссылке, указанной ниже</p>

    <p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>
</div>
