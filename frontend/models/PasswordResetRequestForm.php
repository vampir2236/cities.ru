<?php
namespace frontend\models;

use common\behaviors\FormattedError;
use common\models\User;
use yii\base\Model;

/**
 * Форма запроса на восстановление пароля
 */
class PasswordResetRequestForm extends Model
{
    public $email;


    public function behaviors()
    {
        return [
            FormattedError::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\common\models\User',
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => 'Пользователь с таким email не зарегистрирован.'
            ],
        ];
    }

    /**
     * Отправка письма
     * @return boolean whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $this->email,
        ]);

        if ($user) {
            if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
                $user->generatePasswordResetToken();
            }

            if ($user->save()) {
                return \Yii::$app->mailer->compose(
                    ['html' => 'passwordResetToken-html'],
                    ['user' => $user])
                    ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
                    ->setTo($this->email)
                    ->setSubject('Восстановление пароля для сайта "' . \Yii::$app->name . '"')
                    ->send();
            }
        }

        return false;
    }
}
