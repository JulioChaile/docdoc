<?php
/* @var $this View */
/* @var $form ActiveForm */
/* @var $model Estudios */

use common\models\Estudios;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

?>
<div class="modal-dialog">
    <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?= $titulo ?></h4>
        </div>

        <?php $form = ActiveForm::begin(['id' => 'estudios-form',]) ?>

        <div class="modal-body">
            <div id="errores-modal"> </div>

            <?= Html::activeHiddenInput($model, 'IdEstudio') ?>
            
            <?php
            $url = Url::to(['/ciudades/autocompletar-provincias']);

            $initScript = <<<SCRIPT
            function (element, callback) {
                var id=\$(element).val();
                if (id && id !== "" && id != 0) {
                    \$.ajax("{$url}?id=" + id , {
                        dataType: "json"
                    }).done(function(data) { callback(data);});
                }
                else {
                    callback([]);
                }
            }
SCRIPT;
            echo $form->field($model, 'IdProvincia')->widget(Select2::className(), [
                'options' => [
                    'placeholder' => 'Buscar provincia...',
                ],
                'pluginOptions' => [
                    'minimumInputLength' => 3,
                    'allowClear' => true,
                    'ajax' => [
                        'url' => $url,
                        'dataType' => 'json',
                        'data' => new JsExpression('function(param){ return { cadena : param.term }}'),
                        'processResults' => new JsExpression('function(data,page) { return {results:data}; }')
                    ],
                    'initSelection' => new JsExpression($initScript),
                ]
            ]);
            ?>
            
            <?php
            $url = Url::to(['/ciudades/autocompletar']);

            $initScript = <<<SCRIPT
            function (element, callback) {
                var id=\$(element).val();
                if (id && id !== "" && id != 0) {
                    \$.ajax("{$url}?id=" + id , {
                        dataType: "json"
                    }).done(function(data) { callback(data);});
                }
                else {
                    callback([]);
                }
            }
SCRIPT;
            echo $form->field($model, 'IdCiudad')->widget(Select2::className(), [
                'options' => [
                    'placeholder' => 'Buscar ciudad...',
                ],
                'pluginOptions' => [
                    'minimumInputLength' => 3,
                    'allowClear' => true,
                    'ajax' => [
                        'url' => $url,
                        'dataType' => 'json',
                        'data' => new JsExpression('function(param){ return { idProvincia: $("#estudios-idprovincia").val(),'
                                . ' cadena : param.term }}'),
                        'processResults' => new JsExpression('function(data,page) { return {results:data}; }')
                    ],
                    'initSelection' => new JsExpression($initScript),
                ]
            ]);
            ?>
            
            <?= $form->field($model, 'Estudio')->textInput(['autocomplete' => 'off']) ?>

            <?= $form->field($model, 'Domicilio')->textInput(['autocomplete' => 'off']) ?>
            
            <?= $form->field($model, 'Telefono')->textInput(['autocomplete' => 'off']) ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <?= Html::submitButton('Guardar', ['class' => 'btn btn-primary',]) ?>  
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

