<?php

namespace frontend\models;

use common\models\User;
use yii\base\InvalidParamException;
use yii\base\Model;
use Yii;

/**
 * Модель подтверждения email
 */
class ConfirmEmailForm extends Model
{
    private $_user;


    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidParamException('Ошибочная ссылка подтверждения email.');
        }
        $this->_user = User::findByEmailConfirmToken($token);
        if (!$this->_user) {
            throw new InvalidParamException('Ошибочная ссылка подтверждения email.');
        }
        parent::__construct($config);
    }


    /**
     * Подтверждение email пользователя
     * @return bool
     */
    public function confirmEmail()
    {
        $user = $this->_user;
        $user->status = User::STATUS_ACTIVE;
        $user->removeEmailConfirmToken();
        Yii::$app->user->login($user);

        return $user->save();
    }
}
