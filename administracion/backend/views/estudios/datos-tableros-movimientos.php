<?php
/* @var $this View */
/* @var $form ActiveForm */
/* @var $model Origenes */

use common\models\Origenes;
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

        <?php $form = ActiveForm::begin(['id' => 'origenes-form',]) ?>

        <div class="modal-body">
            <div id="errores-modal"> </div>

            <?= Html::activeHiddenInput($model, 'IdTipoMovimientoTablero') ?>
            <?= Html::activeHiddenInput($model, 'IdEstudio') ?>
            
            <?= $form->field($model, 'IdTipoMov')->label('Tipo de Movimiento')->dropDownList(
                $tiposMov,
                [
                    'prompt' => 'Seleccionar Tipo de Movimiento'
                ]
            ) ?>

            <?= $form->field($model, 'Orden')->label('Orden')->dropDownList(
                $ordenes,
                [
                    'prompt' => 'Seleccionar Orden'
                ]
            ) ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <?= Html::submitButton('Guardar', ['class' => 'btn btn-primary',]) ?>  
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

