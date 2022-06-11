<?php

use common\models\Estudios;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $busqueda common\models\forms\BusquedaForm */

$this->title = $estudio->Estudio;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <?php $form = ActiveForm::begin(['id' => 'estudios-form']) ?>
            <?= Html::activeHiddenInput($estudio, 'IdEstudio') ?>
            <div class="box-body">
                <div class="col-md-12">
                    <div class="col-md-6">
                        <?= $form->field($estudio, 'Estudio') ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($estudio, 'Domicilio') ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($estudio, 'Telefono') ?>
                    </div>
                    <div class="col-md-6">
                        <?= Html::submitButton('Guardar', ['class' => 'btn btn-primary pull-right']) ?>
                    </div>
                </div>
            <?php ActiveForm::end() ?>   
            </div>
        </div>
    </div> 
</div>
