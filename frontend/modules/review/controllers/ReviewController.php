<?php

namespace frontend\modules\review\controllers;

use common\components\FindCityByIp;
use common\models\User;
use frontend\modules\city\models\City;
use frontend\modules\review\models\ChooseCity;
use Imagine\Image\Point;
use Yii;
use frontend\modules\review\models\Review;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;


/**
 * Конроллер отзывов
 */
class ReviewController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['create', 'update', 'delete',
                    'city-create', 'author', 'delete-image'],
                'rules' => [
                    [
                        'actions' => ['create', 'update', 'delete',
                            'city-create', 'author', 'delete-image'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'city-create' => ['post'],
                ],
            ],
        ];
    }


    /**
     * Проверка валидна ли сессия выбора города
     * @return bool
     */
    private function isValidSession()
    {
        $startTime = Yii::$app->session->get('start-time', 0);
        return $startTime + Yii::$app->params['validSessionTime'] > time();
    }


    /**
     * Выбор города
     * @return string
     */
    public function actionChooseCity($city = null)
    {
        if (isset($city)) {
            $session = Yii::$app->session;
            $session->set('city', $city);
            $session->set('start-time', time());

            return $this->goHome();
        } else {
            $currentCity = FindCityByIp::find(Yii::$app->request->userIP);
            $cities = ArrayHelper::getColumn(Review::getAllCities(), 'name');

            return $this->renderAjax('chooseCity', [
                'currentCity' => $currentCity,
                'cities' => $cities,
            ]);
        }
    }


    /**
     * Список отзывов
     * @param null $id_author
     * @return string
     */
    public function actionIndex($id_author = null)
    {
        $city = null;
        $author = null;
        $dataProvider = null;
        $isValidSession = $this->isValidSession();

        if ($isValidSession) {
            if (isset($id_author)) {
                $query = Review::find()->byAuthor($id_author);
                $author = User::getById($id_author);
            } else {
                $city = Yii::$app->session->get('city');
                $query = Review::find()->byCity(City::getIdCityByName($city));
            }

            $dataProvider = new ActiveDataProvider([
                'query' => $query->orderBy('created_at DESC'),
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]);
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'author' => $author,
            'city' => $city,
            'isValidSession' => $isValidSession,
        ]);
    }


    /**
     * Просмотр отзыва
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }


    /**
     * Добавление отзыва
     * @return mixed
     */
    public function actionCreate()
    {
        $review = new Review();
        if ($review->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($review->validate()) {
                $review->uploadImage(Yii::$app->params['uploadPath']);
                $review->save(false);
                return 'success';
            } else {
                return $review->getFormattedError();
            }
        }

        $cities = ArrayHelper::map(
            City::find()->orderBy('name ASC')->asArray()->all(),
            'id', 'name');
        $createdCity = new City();

        return $this->renderAjax('create', [
            'review' => $review,
            'cities' => $cities,
            'createdCity' => $createdCity,
        ]);
    }


    /**
     * Редактирование отзыва
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $review = $this->findModel($id);
        if ($review->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($review->validate()) {
                $review->uploadImage(Yii::$app->params['uploadPath']);
                $review->save(false);
                return 'success';
            } else {
                return $review->getFormattedError();
            }
        }

        $review->ids_city = ArrayHelper::getColumn(
            $review->cities, 'id');
        $cities = ArrayHelper::map(City::getAllCities(),
            'id', 'name');
        $createdCity = new City();
        $imagePreview = $this->getImagePreview($review);

        return $this->renderAjax('update', [
            'review' => $review,
            'cities' => $cities,
            'imagePreview' => $imagePreview,
            'createdCity' => $createdCity,
        ]);
    }


    /**
     * Удаление отзыва
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->delete()) {
            $model->deleteImage(Yii::$app->params['uploadPath']);
        };

        return $this->redirect(['index']);
    }


    /**
     * Добавление нового города из Кладр через Ajax
     * @return array
     */
    public function actionCityCreate()
    {
        $model = new City();
        if ($model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->save()) {
                return [
                    'status' => 'success',
                    'id' => $model->id,
                    'name' => $model->name,
                ];
            } else {
                return $model->getFormattedError();
            }
        }
    }


    /**
     * Просмотр информации об авторе
     * @param $id
     * @return string
     */
    public function actionAuthor($id)
    {
        $model = User::getById($id);

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('author', [
                'model' => $model,
            ]);
        } else {
            return $this->render('author', [
                'model' => $model,
            ]);
        }
    }


    /**
     * Удаление изображения через Ajax
     * @param $id
     */
    public function actionDeleteImage()
    {
        $id = Yii::$app->request->post('key');
        $model = $this->findModel($id);

        if ($model) {
            $model->deleteImage(Yii::$app->params['uploadPath']);

            $model->img = null;
            $model->save(false);
        }

        return true;
    }


    /**
     * Настройки для виджета просмотра изображения (FileInput)
     * @param $model
     * @return array
     */
    private function getImagePreview($model)
    {
        $imagePreview = [
            'initialPreview' => [],
            'initialPreviewConfig' => [],
        ];
        if (isset($model->img)
            && file_exists(Yii::$app->params['uploadPath']) . $model->img
        ) {
            $imagePreview['initialPreview'][] = Html::img(
                Yii::$app->params['uploadUrl'] . $model->img, [
                'class' => 'file-preview-image',
            ]);
            $imagePreview['initialPreviewConfig'][] = [
                'url' => Url::to(['delete-image']),
                'key' => $model->id,
            ];
        }
        return $imagePreview;
    }


    /**
     * Поиск отзыва по ключу
     * @param integer $id
     * @return Review the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (!Yii::$app->user->isGuest) {
            $model = Review::findOne($id);
        } else {
            $model = Yii::$app->db->cache(function ($db) use ($id) {
                return Review::findOne($id);
            }, Yii::$app->params['dbCacheValidTime']);
        }

        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}