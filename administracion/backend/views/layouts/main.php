<?php

/* @var $this View */
/* @var $content string */

use backend\assets\AppAsset;
use common\widgets\Alert;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;

AppAsset::register($this);
$this->registerJs('Main.init()');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.7.14"></script>
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
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            ['label' => 'Padron', 'url' => ['/padron/index']],
            ['label' => 'Consultas', 'url' => ['/consultas/index']],
            ['label' => 'Casos', 'url' => ['/casos/index']],
            ['label' => 'Estudios', 'url' => ['/estudios/index']],
            ['label' => 'Mediadores', 'url' => ['/mediadores/index']],
            [
                'label' => 'Parámetros', 'url' => ['#'],
                'items' => [
                    ['label' => 'Jurisdicciones', 'url' => ['/jurisdicciones/index']],
                    ['label' => 'Competencias', 'url' => ['/competencias/index']],
                    ['label' => 'Tipos de caso', 'url' => ['/tipos-caso/index']],
                    ['label' => 'Tipos de Procesos y Nominaciones', 'url' => ['/juzgados/index']],
                    // ['label' => 'Origenes', 'url' => ['/origenes/index']],
                    ['label' => 'Cias de seguro', 'url' => ['/cias-seguro/index']],
                    ['label' => 'Estados de Procesos', 'url' => ['/estado-ambito-gestion/index']],
                    ['label' => 'Resoluciones SMVM', 'url' => ['/resoluciones/index']],
                    ['label' => 'Difusiones', 'url' => ['/difusiones/index']],
                    ['label' => 'Estados de Casos Pendientes', 'url' => ['/estado-casos-pendientes/index']]
                ]
            ],
            [
                'label' => 'Sistema', 'url' => ['#'],
                'items' => [
                    ['label' => 'Empresa', 'url' => ['/empresa/index']],
                    ['label' => 'Roles', 'url' => ['/roles/index']],
                    ['label' => 'Usuarios', 'url' => ['/usuarios/index']]
                ]
            ],
            Yii::$app->user->isGuest ? (
                ['label' => 'Login', 'url' => ['/usuarios/login']]
            ) : (
                '<li>'
                . Html::beginForm(['/usuarios/logout'], 'post')
                . Html::submitButton(
                    'Logout (' . Yii::$app->user->identity->Apellidos.', '.Yii::$app->user->identity->Nombres. ')',
                    ['class' => 'btn btn-link logout']
                )
                . Html::endForm()
                . '</li>'
            )
        ],
    ]);
    NavBar::end();
    ?>

    <div class="container" style="padding-top: 5em" >
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
