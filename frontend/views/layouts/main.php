<?php

use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Url;
use frontend\assets\AppAsset;
use frontend\widgets\Alert;
use yii\widgets\Breadcrumbs;

AppAsset::register($this);

?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar navbar-default navbar-custom navbar-fixed-top',
        ],
    ]);
    $menuItems[] = ['label' => 'Главная', 'url' => ['/']];
    $menuItems[] = ['label' => 'Выбор города', 'url' => ['/review/choose-city'],
        'linkOptions' => [
            'data-dismiss' => 'modal',
            'data-target' => '#modal-form',
            'data-toggle' => 'modal',
        ]];
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => 'Регистрация', 'url' => ['/site/signup'],
            'linkOptions' => [
                'data-dismiss' => 'modal',
                'data-target' => '#modal-form',
                'data-toggle' => 'modal',
            ]];
        $menuItems[] = ['label' => 'Войти', 'url' => ['/site/login'],
            'linkOptions' => [
                'data-dismiss' => 'modal',
                'data-target' => '#modal-form',
                'data-toggle' => 'modal',
            ]];
    } else {
        $menuItems[] = [
            'label' => 'Выйти (' . Yii::$app->user->identity->fio . ')',
            'url' => ['/site/logout'],
            'linkOptions' => ['data-method' => 'post']
        ];
    }
    echo Nav::widget([
        'options' => ['class' => 'nav navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <header class="intro-header" style="background-image: url(<?= Url::to('@web/' . 'img/home-bg.jpg'); ?>)">
        <div class="container">
            <div class="site-heading">
                <h1><?= Yii::$app->name; ?></h1>
                <hr class="small">
                <span class="subheading">Оставь отзыв о своем городе!</span>
            </div>
        </div>
    </header>


    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ?
                $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer>
    <div class="container">
        <hr>
        <p class="copyright text-muted">
            &copy; <?= Yii::$app->name . ' ' . date('Y') ?>
        </p>
    </div>
</footer>


<?= Modal::widget([
    'clientOptions' => false,
    'options' => [
        'id' => 'modal-form',
        'tabindex' => false,
    ]
]); ?>


<?= Html::img(Url::to('@web/img/preloader.gif'), [
    'id' => 'preloader',
    'style' => 'display: none',
]); ?>

<?php $this->endBody() ?>
</body>

</html>

<?php $this->endPage() ?>
