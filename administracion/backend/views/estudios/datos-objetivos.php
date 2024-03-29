<?php
/* @var $this View */
/* @var $form ActiveForm */
/* @var $model EstadosCaso */

use common\models\ObjetivosEstudio;
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

        <?php $form = ActiveForm::begin(['id' => 'objetivosestudio-form',]) ?>

        <div class="modal-body">
            <div id="errores-modal"> </div>

            <?= Html::activeHiddenInput($objetivo, 'IdObjetivoEstudio') ?>
            
            <?= Html::activeHiddenInput($objetivo, 'IdEstudio') ?>
            
            <?= $form->field($objetivo, 'ObjetivoEstudio') ?>

            <?= $form->field($objetivo, 'IdTipoMov')->label('Tipo de Movimiento')->dropDownList(
                $tiposMov,
                [
                    'prompt' => 'Seleccionar Tipo de Movimiento'
                ]
            ) ?>

            <?= $form->field($objetivo, 'ColorMov')->label('Estado de Gestión')->dropDownList(
                [
                    'negative' => 'Perentorios',
                    'primary' => 'Gestion Estudio',
                    'warning' => 'Gestion Externa',
                    'positive' => 'Finalizados',
                ],
                ['prompt' => 'Seleccionar Estado de Gestión']
            ) ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <?= Html::submitButton('Guardar', ['class' => 'btn btn-primary',]) ?>  
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

