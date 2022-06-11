<?php

use common\models\Usuarios;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $model Usuarios */

$this->title = 'Ingresar';
?>
<h3 class="login-box-msg"><?= Html::encode($this->title) ?></h3>

<div class="login-box-body">
    <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
    <div>
        <?php
        foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
            echo '<div class="alert alert-' . $key . ' alert-dismissable">'
            . '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'
            . $message . '</div>';
        }

        ?>
        <div class="form-group">
            <?= $form->field($model, 'Usuario')->textInput(['autofocus' => true, 'autocomplete' => 'off']) ?>
        </div>
        <div class="form-group">
            <?= $form->field($model, 'Password')->passwordInput(['autocomplete' => 'off']) ?>
        </div>
    </div>
    <?= Html::submitButton('Login', ['class' => 'btn btn-primary btn-block', 'name' => 'login-button']) ?>
    <?php ActiveForm::end(); ?>
</div>