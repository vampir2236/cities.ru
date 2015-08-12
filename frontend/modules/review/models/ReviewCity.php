<?php

namespace frontend\modules\review\models;

use Yii;

/**
 * This is the model class for table "review_city".
 *
 * @property integer $id
 * @property integer $id_review
 * @property integer $id_city
 * @property City $idCity
 * @property Review $idReview
 */
class ReviewCity extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'review_city';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_review', 'id_city'], 'required'],
            [['id_review', 'id_city'], 'integer'],
            [['id_review', 'id_city'], 'unique',
                'targetAttribute' => ['id_review', 'id_city'],
                'message' => 'Город уже выбран для отзыва.']
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_review' => 'Отзыв',
            'id_city' => 'Город',
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdCity()
    {
        return $this->hasOne(City::className(), ['id' => 'id_city']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdReview()
    {
        return $this->hasOne(Review::className(), ['id' => 'id_review']);
    }
}
