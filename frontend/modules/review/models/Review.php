<?php

namespace frontend\modules\review\models;

use common\behaviors\FormattedError;
use common\models\User;
use frontend\modules\city\models\City;
use Imagine\Image\Point;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseFileHelper;
use yii\imagine\Image;
use yii\web\UploadedFile;

/**
 * Модель отзыва
 *
 * @property integer $id
 * @property integer $id_author
 * @property string $title
 * @property string $text
 * @property integer $rating
 * @property string $img
 * @property integer $created_at
 * @property integer $updated_at
 * @property User $idAuthor
 * @property ReviewCity[] $reviewCities
 */
class Review extends \yii\db\ActiveRecord
{
    const MAX_IMAGE_WIDTH = 800;
    public $image;
    public $ids_city = [];


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'review';
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
            [['title', 'text', 'rating'], 'required'],
            ['title', 'string', 'min' => 2, 'max' => 255],
            ['text', 'string', 'min' => 2, 'max' => 255],
            ['rating', 'integer', 'min' => 1, 'max' => 5, 'integerOnly' => true,
                'tooSmall' => 'Укажите рейтинг города'],
            [
                'ids_city', 'exist',
                'allowArray' => true,
                'targetClass' => City::className(),
                'targetAttribute' => 'id',
            ],
            [['image'], 'file', 'skipOnEmpty' => true,
                'extensions' => 'png, jpg',
                // не получилось настроить проверку на XAMPP
                'checkExtensionByMimeType' => false,
            ],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_author' => 'Автор',
            'title' => 'Название',
            'text' => 'Текст',
            'rating' => 'Рейтинг',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата редактирования',
            'img' => 'Изображение',
            'image' => 'Изображение',
            'ids_city' => 'Город',
        ];
    }


    /**
     * @inheritdoc
     * @return ActiveQuery the newly created [[ActiveQuery]] instance.
     */
    public static function find()
    {
        return new ReviewQuery(get_called_class());
    }


    /**
     * Список городов для выбора
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getAllCities()
    {
        $query = Review::find()
            ->distinct()
            ->joinWith('cities')
            ->select('city.*')
            ->where(['IS NOT', 'city.id', null])
            ->orderBy('city.name')
            ->asArray();

        if (!Yii::$app->user->isGuest) {
            return $query->all();
        } else {
            return Yii::$app->db->cache(function ($db) use ($query) {
                return $query->all();
            }, Yii::$app->params['dbCacheValidTime']);
        }
    }


    /**
     * Получение автора отзыва
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'id_author']);
    }


    /**
     * Получение записи связи города и отзыва
     * @return \yii\db\ActiveQuery
     */
    public function getReviewCities()
    {
        return $this->hasMany(ReviewCity::className(), ['id_review' => 'id']);
    }


    /**
     * Получение городов отзыва
     * @return static
     */
    public function getCities()
    {
        return $this->hasMany(City::className(), ['id' => 'id_city'])
            ->viaTable('review_city', ['id_review' => 'id']);
    }


    /**
     * Сохранение выбранных городов
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        $oldCities = ArrayHelper::getColumn($this->cities, 'id');
        $newCities = empty($this->ids_city) ? [] : $this->ids_city;

        $diff = array_diff($oldCities, $newCities);
        foreach ($diff as $idCity) {
            $city = City::getById($idCity);
            $this->unlink('cities', $city, true);
        }

        $diff = array_diff($newCities, $oldCities);
        foreach ($diff as $idCity) {
            $city = City::getById($idCity);
            $this->link('cities', $city);
        }

        parent::afterSave($insert, $changedAttributes);
    }


    /**
     * Перед сохранение уставаливаем автора отзыва
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->id_author = Yii::$app->user->id;
            }
            return true;
        } else {
            return false;
        }
    }


    /**
     * Загрузка изображения
     * @param $imagesPath
     */
    public function uploadImage($imagesPath)
    {
        $this->image = UploadedFile::getInstance($this, 'image');
        if ($this->image) {
            if (isset($this->img)) {
                $this->deleteImage($imagesPath);
            }
            $this->img = Yii::$app->security->generateRandomString(10)
                . '.' . $this->image->getExtension();

            BaseFileHelper::createDirectory($imagesPath);
            $filename = $imagesPath . $this->img;
            $this->image->saveAs($filename);

            $this->processImage($filename, self::MAX_IMAGE_WIDTH);
        }
    }


    /**
     * Обработка изображения (уменьшение до макс. ширины,
     * понижение качества изображения)
     * @param $filename
     * @param $maxWidth
     */
    private function processImage($filename, $maxWidth)
    {
        $img = Image::frame($filename, 0);
        $size = $img->getSize();

        if ($size->getWidth() > $maxWidth) {
            $img->resize($size->widen($maxWidth));
        }
        $img->save($filename, ['quality' => 70]);
    }


    /**
     * Удаление изображения
     * @param $imagesPath
     */
    public function deleteImage($imagesPath)
    {
        if (!$this->img) {
            return false;
        }

        $filename = $imagesPath . $this->img;
        if (file_exists($filename)) {
            unlink($filename);
        }
        return true;
    }

}


class ReviewQuery extends ActiveQuery
{
    /**
     * Поиск по автору
     * @param $idAuthor
     */
    public function byAuthor($idAuthor)
    {
        return $this->andWhere(['id_author' => $idAuthor]);
    }


    /**
     * Получить отзывы по городу или все отзывы без указания города
     * @param $idCity
     * @return $this
     */
    public function byCity($idCity)
    {
        return $this->joinWith('cities')
            ->where(['city.id' => $idCity])
            ->orWhere(['city.id' => null]);
    }

}