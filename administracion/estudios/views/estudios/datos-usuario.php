<?php
/* @var $this View */
/* @var $form ActiveForm */
/* @var $model UsuariosEstudio */

use common\models\UsuariosEstudio;
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
            <h4 class="modal-title">Agregar abogado</h4>
        </div>

        <?php $form = ActiveForm::begin(['id' => 'estudios-form',]) ?>

        <div class="modal-body">
            <div id="errores-modal"></div>
            <?= Html::activeHiddenInput($model, 'IdEstudio') ?>            
            
            <?php
            $url = Url::to(['/estudios/autocompletar-usuarios']);

            echo $form->field($model, 'IdUsuarioPadre')->widget(Select2::className(), [
                'options' => [
                    'placeholder' => 'Usuarios jerárquico',
                ],
                'pluginOptions' => [
                    'minimumInputLength' => 3,
                    'allowClear' => true,
                    'ajax' => [
                        'url' => $url,
                        'dataType' => 'json',
                        'data' => new JsExpression("function(param){ return { idEstudio:  $('#usuariosestudio-idestudio').val(),"
                                . " cadena : param.term }}"),
                        'processResults' => new JsExpression('function(data,page) { return {results:data}; }')
                    ],

                ]
            ]);
            ?>
            
            <?= $form->field($model, 'IdRolEstudio')->widget(Select2::className(), [
                'options' => [
                    'placeholder' => 'Seleccionar rol',
                ],
                'data' => ArrayHelper::map($roles, 'IdRolEstudio', 'RolEstudio'),
                'pluginOptions' => [
                    'allowClear' => true,
                ]
            ]);
            ?>
            
            <?= $form->field($model, 'Apellidos')->textInput(['autocomplete' => 'off']) ?>
            
            <?= $form->field($model, 'Nombres')->textInput(['autocomplete' => 'off']) ?>
            
            <?= $form->field($model, 'Usuario')->textInput(['autocomplete' => 'off']) ?>
            
            <?= $form->field($model, 'Email')->textInput(['autocomplete' => 'off']) ?>
            
            <?= $form->field($model, 'Observaciones')->textarea() ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <?= Html::submitButton('Guardar', ['class' => 'btn btn-primary',]) ?>  
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

