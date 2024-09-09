<?php

/** @var yii\web\View $this */
/** @var string $content */

use webvimark\modules\UserManagement\components\GhostMenu;
use webvimark\modules\UserManagement\components\GhostNav;
use webvimark\modules\UserManagement\UserManagementModule;
use yii\helpers\Html;
use yii\bootstrap\NavBar;
use yii\bootstrap\Nav;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" >
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header>
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    echo GhostNav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'encodeLabels'=>false,
        'activateParents'=>true,
        'items' => [
            [
                'label' => 'Feedback',
                'items'=>[
                    ['label'=>'Criar', 'url'=>['/feedback/create']],
                    ['label'=>'Listar', 'url'=>['/feedback/index']],
                ],
            ],
            [
                'label' => 'Usuário',
                'items'=>[
                    ['label'=>'Alterar Senha', 'url'=>['/user-management/auth/change-own-password']],
                    ['label'=>'Confirmação de E-mail', 'url'=>['/user-management/auth/confirm-email']],
                ],
            ],
            [
                'label' => 'Admin',
                'items'=>UserManagementModule::menuItems()
            ],
            Yii::$app->user->isGuest ?
                ['label' => 'Login','url' => ['/user-management/auth/login']] :
                ['label' => 'Sair (' . Yii::$app->user->identity->username . ')',
                    'url' => ['/user-management/auth/logout'],
                    'linkOptions' => ['data-method' => 'post']],
        ],
    ]);
    NavBar::end();

    ?>
</header>

<main role="main" class="flex-shrink-0">
    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
</main>

<footer class="footer">
    <div class="container">
        <p class="pull-left">Ciência da Computação</p>
        <p class="pull-right">IFG Anápolis <?= date('Y') ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
