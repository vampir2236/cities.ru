<?php

namespace common\components;

use common\helpers\AjaxRequest;
use yii\base\Component;
use yii\helpers\Html;


class FindCityByIp extends Component
{
    /**
     * Найти город по ip адресу
     * @param $ip
     * @return bool
     */
    public static function find($ip)
    {
        $url = 'http://api.sypexgeo.net/json/' . Html::encode($ip);
        $city = json_decode(AjaxRequest::execute($url), true);

        if ($city['city'] && isset($city['city']['name_ru'])) {
            return $city['city']['name_ru'];
        } else {
            return false;
        }
    }
}