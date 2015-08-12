<?php
namespace frontend\models;

use common\behaviors\FormattedError;
use common\models\User;
use yii\base\Model;
use Yii;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $email;
    public $password;
    public $fio;
    public $phone;
    public $verifyCode;

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
            [['email', 'fio'], 'filter', 'filter' => 'trim'],
            [['email', 'password', 'fio', 'phone', 'verifyCode'], 'required'],

            ['email', 'email'],
            ['email', 'unique', 'targetClass' => '\common\models\User',
                'message' => 'Пользователь с таким email уже зарегистрирован.'],

            ['fio', 'required'],
            ['fio', 'string', 'min' => 2, 'max' => 255],

            ['phone', 'match', 'pattern' => '/^\+7 \(\d{3}\) \d{3}\-\d{2}\-\d{2}$/'],

            ['password', 'string', 'min' => 6],

            ['verifyCode', 'captcha', 'captchaAction' => 'site/captcha'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => 'Email',
            'fio' => 'ФИО',
            'phone' => 'Телефон',
            'password' => 'Пароль',
            'verifyCode' => 'Проверочный код',
        ];
    }


    private function sendEmailConfirmToken(User $user)
    {
        return Yii::$app->mailer->compose('emailConfirmToken', ['user' => $user])
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
            ->setTo($this->email)
            ->setSubject('Активация аккаунта (' . Yii::$app->name . ')')
            ->send();
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if ($this->validate()) {
            $user = new User();
            $user->email = $this->email;
            $user->fio = $this->fio;
            $user->phone = $this->phone;
            $user->status = User::STATUS_NOT_ACTIVE;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            $user->generateEmailConfirmToken();

            return $user->save() && $this->sendEmailConfirmToken($user);
        }

        return false;
    }
}
