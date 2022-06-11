<?php
/* @var $this View */
/* @var $form ActiveForm */
/* @var $model Difusiones */

use backend\models\Difusiones;
use dosamigos\datepicker\DateRangePicker;
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

        <?php $form = ActiveForm::begin(['id' => 'difusiones-form',]) ?>

        <div class="modal-body">
            <div id="errores-modal"> </div>

            <?= Html::activeHiddenInput($model, 'IdDifusion') ?>
            
            <?= $form->field($model, 'Difusion') ?>
            
            <?=
                $form->field($model, 'FechaInicio')->widget(DateRangePicker::className(), [
                    'attributeTo' => 'FechaFin',
                    'labelTo' => 'al',
                    'form' => $form,
                    'language' => 'es',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'dd/mm/yyyy'
                    ]
                ])->label('')
            ?>   
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <?= Html::submitButton('Guardar', ['class' => 'btn btn-primary',]) ?>  
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>