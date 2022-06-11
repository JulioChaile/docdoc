<?php
/* @var $this View */
/* @var $form ActiveForm */
/* @var $model CalendariosEstudio */

use common\models\CalendariosEstudio;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

?>
<div class="modal-dialog">
    <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Agregar calendario</h4>
        </div>

        <?php $form = ActiveForm::begin(['id' => 'calendario-form',]) ?>

        <div class="modal-body">
            <div id="errores-modal"></div>
            <?= Html::activeHiddenInput($model, 'IdEstudio') ?>
            
            <?= $form->field($model, 'Titulo')->textInput(['autocomplete' => 'off']) ?>
            
            <?= $form->field($model, 'Descripcion')->textInput(['autocomplete' => 'off']) ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <?= Html::submitButton('Guardar', ['class' => 'btn btn-primary',]) ?>  
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

