<?php

namespace frontend\modules\city\models;

use common\behaviors\FormattedError;
use common\components\Kladr;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * Модель городов
 *
 * @property integer $id
 * @property string $name
 * @property integer $created_at
 * @property integer $updated_at
 * @property ReviewCity[] $reviewCities
 */
class City extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'city';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            FormattedError::className(),
        ];
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            [['name'], 'unique', 'message' => 'Такой город уже есть'],
            ['name', function ($attribute, $params) {
                $kladr = new Kladr;
                $cities = $kladr->getCities($this->$attribute);
                foreach ($cities as $city) {
                    if ($city === $this->$attribute) return;
                }
                $this->addError($attribute, 'Указанный город не найден в КЛАДР');
            }]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата редактирования',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReviewCities()
    {
        return $this->hasMany(ReviewCity::className(), ['id_city' => 'id']);
    }


    /**
     * Получить ключ города по названию
     * @param $name
     * @return mixed|null
     */
    public static function getIdCityByName($name)
    {
        $city = Yii::$app->db->cache(function ($db) use ($name) {
            return City::find()->where(['name' => $name])->one();
        }, Yii::$app->params['dbCacheValidTime']);

        return isset($city) ? $city->id : null;
    }


    /**
     * Список всех гоородов
     * @return mixed
     * @throws \Exception
     */
    public static function getAllCities()
    {
        if (!Yii::$app->user->isGuest) {
            return City::find()->orderBy('name ASC')->asArray()->all();
        } else {
            return Yii::$app->db->cache(function ($db) {
                return City::find()->orderBy('name ASC')->asArray()->all();
            }, Yii::$app->params['dbCacheValidTime']);
        }
    }


    /**
     * Получить город по ключу
     * @param $idCity
     * @return mixed
     * @throws \Exception
     */
    public static function getById($idCity)
    {
        return Yii::$app->db->cache(function ($db) use ($idCity) {
            return City::findOne($idCity);
        }, Yii::$app->params['dbCacheValidTime']);
    }
}
