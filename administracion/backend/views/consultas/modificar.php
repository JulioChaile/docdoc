<?php
/* @var $this View */
/* @var $form ActiveForm */
/* @var $model Consultas */

use common\models\Consultas;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

?>
<div class="modal-dialog">
    <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Modificar consulta</h4>
        </div>

        <?php $form = ActiveForm::begin(['id' => 'derivarconsultas-form',]) ?>

        <div class="modal-body">
            <div id="errores-modal"> </div>

            <?= Html::activeHiddenInput($model, 'IdConsulta') ?>
            
            <?= $form->field($model, 'IdDifusion')->widget(Select2::className(), [
                'options' => [
                    'placeholder' => 'Buscar campaña...',
                ],
                'data' => ArrayHelper::map($difusiones, 'IdDifusion', 'Difusion'),
                'pluginOptions' => [
                    'allowClear' => true,
                ]
            ]);
            ?>
            
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <?= Html::submitButton('Guardar', ['class' => 'btn btn-primary',]) ?>  
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>