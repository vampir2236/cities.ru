<?php


namespace common\behaviors;


use yii\base\Behavior;
use yii\helpers\Html;


class FormattedError extends Behavior
{

    /**
     * Получение ошибок валидации в нужном формате для отобржения
     * на клиенте
     * @return array
     */
    public function getFormattedError()
    {
        $result = [];

        foreach ($this->owner->getErrors() as $attribute => $errors) {
            $result[Html::getInputId($this->owner, $attribute)] = $errors;
        }
        return $result;
    }
}