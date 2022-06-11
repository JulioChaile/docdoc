<?php
/* @var $this View */
/* @var $form ActiveForm */
/* @var $model Empresa */

use common\models\Empresa;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

?>
<div class="modal-dialog">
    <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Modificar Parámetro</h4>
        </div>

        <?php $form = ActiveForm::begin(['id' => 'empresa-form',]) ?>

        <div class="modal-body">
            <div id="errores-modal"> </div>

            <?= Html::activeHiddenInput($empresa, 'IdEstudio') ?>
            
            <?= $form->field($empresa, 'Parametro')->textInput(['readonly' => true]) ?>
            
            <?= $form->field($empresa, 'Descripcion')->textarea(['readonly' => true]) ?>
            
            <?= $form->field($empresa, 'Valor') ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <?= Html::submitButton('Guardar', ['class' => 'btn btn-primary',]) ?>  
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

