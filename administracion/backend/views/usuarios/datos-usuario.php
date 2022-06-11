<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Usuarios */
/* @var $form ActiveForm */
$this->title = $titulo;
?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?= $titulo ?></h4>
            
        </div>
        <div id="errores-modal"></div>
        <?php $form = ActiveForm::begin(['layout' => 'horizontal', 'id' => 'datosusuario-form']); ?>
        <div class="modal-body">
            <?= Html::activeHiddenInput($model, 'IdUsuario') ?>
            
            <?= $form->field($model, 'IdRol')->dropDownList(ArrayHelper::map($listadoRoles, 'IdRol', 'Rol'))?>
            
            <?= $form->field($model, 'Usuario') ?>
            
            <?= $form->field($model, 'Apellidos') ?>
            
            <?= $form->field($model, 'Nombres') ?>
            
            <?= $form->field($model, 'Email') ?>

            <?= $form->field($model, 'TelefonoUsuario') ?>
            
            <?= $form->field($model, 'Observaciones')->textarea() ?>
            <div class="clearfix"></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <?= Html::submitButton('Guardar', ['class' => 'btn btn-primary', 'name' => 'rol-button']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
