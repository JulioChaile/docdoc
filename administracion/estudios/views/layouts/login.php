<?php

use estudios\assets\AppAsset;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $content string */

AppAsset::register($this);
$this->title = 'DocDoc Estudios';
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <?= Html::csrfMetaTags() ?>
        <title><?= Yii::$app->name ?> | <?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body style="background-color: #a3ceed">
        <?php $this->beginBody() ?>
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <div class="login-box">
                <h1 class="login-logo" style="text-align: center">
                    <img src="<?= Yii::$app->session->get('Parametros')['LOGO'] ?>" width="350"
                         alt="<?= Yii::$app->session->get('Parametros')['EMPRESA'] ?>"/> 
                </h1>
                <?= $content ?>
            </div>
        </div>
        <div class="col-md-4"></div>
    </body>
    <?php $this->endBody() ?>
</html>
<?php $this->endPage() ?>
