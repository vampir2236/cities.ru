<?php

namespace common\components;

use common\helpers\AjaxRequest;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;


class Kladr extends Component
{

    /**
     * Получить строку запроса поиска городов
     * @param $city
     * @return string
     */
    private function getFindCitiesQuery($city)
    {
        return 'http://kladr-api.ru/api.php?query=' . urlencode($city)
            . '&contentType=city&typeCode=1&limit=10'
            . '&token=55aff6c20a69de7d448b4585';
    }

    /**
     * Получение списка городов из Кладр
     * @param $query
     * @return bool|mixed
     */
    public function getCities($query)
    {
        $url = $this->getFindCitiesQuery($query);

        $result = AjaxRequest::execute($url);
        if (preg_match('/Error: (.*)/', $result, $matches)) {
            return false;
        }

        return ArrayHelper::getColumn(
            json_decode($result, true)['result'],
            'name'
        );
    }

}