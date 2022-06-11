<?php

use common\models\Mediadores;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

?>
<div class="modal-dialog">
    <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?= $titulo ?></h4>
        </div>

        <?php $form = ActiveForm::begin(['id' => 'mediadores-form',]) ?>

        <div class="modal-body">
            <div id="errores-modal"> </div>

            <?= Html::activeHiddenInput($model, 'IdMediador') ?>
            
            <?= $form->field($model, 'Nombre') ?>
            <?= $form->field($model, 'Registro') ?>
            <?= $form->field($model, 'MP') ?>
            <?= $form->field($model, 'Domicilio') ?>
            <?= $form->field($model, 'Telefono') ?>
            <?= $form->field($model, 'Email') ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <?= Html::submitButton('Guardar', ['class' => 'btn btn-primary',]) ?>  
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

