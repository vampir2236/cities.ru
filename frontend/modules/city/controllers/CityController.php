<?php

namespace frontend\modules\city\controllers;

use common\components\Kladr;
use Yii;
use frontend\modules\city\models\City;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Котроллер городов
 */
class CityController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['create', 'delete'],
                'rules' => [
                    [
                        'actions' => ['create', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }


    /**
     * Список городов
     * @return mixed
     */
    public function actionIndex()
    {
        //sql
        $dataProvider = new ActiveDataProvider([
            'query' => City::find()->orderBy('name ASC'),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Просмотр города
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->renderAjax('view', [
            'model' => $this->findModel($id),
        ]);
    }


    /**
     * Добавление города
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new City();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return 'success';
            } else {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
        }

        return $this->renderAjax('create', [
            'model' => $model,
        ]);
    }


    /**
     * Удаление города
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }


    /**
     * Список городов из Кладр для автокомплита
     * @param null $q
     */
    public function actionCityList($q = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $result = [
            'results' => [
                [
                    'id' => '',
                    'text' => '',
                ],
            ]];

        if ($q != null) {
            $kladr = new Kladr;
            $cities = $kladr->getCities($q);
            if ($cities) {
                $results = [];
                foreach ($cities as $city) {
                    $results[] = [
                        'id' => $city,
                        'text' => $city,
                    ];
                }
                if (count($results)) {
                    $result['results'] = $results;
                }
            }
        }
        return $result;
    }

    /**
     * Finds the City model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return City the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = Yii::$app->db->cache(function ($db) use ($id) {
            return City::findOne($id);
        }, Yii::$app->params['dbCacheValidTime']);

        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
