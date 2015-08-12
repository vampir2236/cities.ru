<?php

namespace frontend\controllers;

use frontend\models\ConfirmEmailForm;
use Yii;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }


    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }


    /**
     * Вход на сайт
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $loginForm = new LoginForm();
        $passwordResetRequestForm = new PasswordResetRequestForm();

        if ($loginForm->load(Yii::$app->request->post())) {
            //sql
            if ($loginForm->login()) {
                return $this->goHome();
            } else {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $loginForm->getFormattedError();
            }
        }

        Yii::$app->assetManager->bundles['AppAsset'] = true;
        return $this->renderAjax('login', [
            'loginForm' => $loginForm,
            'passwordResetRequestForm' => $passwordResetRequestForm,
        ]);
    }


    /**
     * Выход с сайта
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }


    /**
     * Регистрация пользователя
     * @return string|Response
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->signup()) {
                Yii::$app->getSession()->setFlash('success',
                    'Вам отправлено письмо со ссылкой для подтверждения email.');
                return $this->goHome();
            } else {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $model->getFormattedError();
            }
        }

        return $this->renderAjax('signup', [
            'model' => $model,
        ]);
    }


    /**
     * Запрос на восстановление пароля
     * @return string|Response
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post())) {
            //sql
            if ($model->validate()) {
                if ($model->sendEmail()) {
                    $message = 'На Ваш email отправлены инструкции по восстановлению пароля.';
                } else {
                    $message = 'Возникла ошибка при отправке письма на Ваш email.';
                }
                Yii::$app->getSession()->setFlash('success', $message);
                return $this->goHome();
            } else {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $model->getFormattedError();
            }
        }

        return $this->renderAjax('requestPasswordResetToken', [
            $model => $model,
        ]);
    }


    /**
     * Ввод нового пароля
     * @param $token
     * @return string|Response
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->setFlash('success', 'Пароль успешно изменен.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
            'resetPassword' => true,
        ]);
    }


    /**
     * Подтверждение email
     * @param $token
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionConfirmEmail($token)
    {
        try {
            $model = new ConfirmEmailForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->confirmEmail()) {
            Yii::$app->getSession()->setFlash('success', 'Ваш email успешно подтвержден.');
        } else {
            Yii::$app->getSession()->setFlash('error', 'Ошибка подтверждения Вашего email.');
        }

        return $this->goHome();
    }
}
