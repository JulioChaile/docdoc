<?php
/* @var $this View */
/* @var $form ActiveForm */
/* @var $model Competencias */

use common\models\Competencias;
use dosamigos\datepicker\DatePicker;
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

        <?php $form = ActiveForm::begin(['id' => 'resoluciones-form',]) ?>

        <div class="modal-body">
            <div id="errores-modal"> </div>

            <?= Html::activeHiddenInput($model, 'IdResolucionSMVM') ?>
            
            <?= $form->field($model, 'Resolucion') ?>

            <?= $form->field($model, 'FechaResolucion')->widget(
                DatePicker::className(), [
                        // modify template for custom rendering
                    // 'template' => '<div class="well well-sm" style="background-color: #fff; width:250px">{input}</div>',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd'
                    ]
            ]);?>

            <?= $form->field($model, 'MontoResolucion') ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <?= Html::submitButton('Guardar', ['class' => 'btn btn-primary',]) ?>  
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

