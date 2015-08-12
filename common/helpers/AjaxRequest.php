<?php

namespace common\helpers;


class AjaxRequest
{
    /**
     * Выполнение и получение результата ajax запроса
     * @param $url
     * @return string
     */
    public static function execute($url)
    {
        $context = stream_context_create([
            'http' => [
                'header' => 'Connection: close\r\n',
            ]
        ]);

        return file_get_contents($url, false, $context);
    }
}