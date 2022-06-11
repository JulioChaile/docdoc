<?php
/* @var $this View */
/* @var $form ActiveForm */
/* @var $model Consultas */

use common\models\Consultas;
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
            <h4 class="modal-title">Derivar consulta</h4>
        </div>

        <?php $form = ActiveForm::begin(['id' => 'derivarconsultas-form',]) ?>

        <div class="modal-body">
            <div id="errores-modal"> </div>

            <?= Html::activeHiddenInput($model, 'IdConsulta') ?>
                        
            <?php
            $url = Url::to(['/estudios/autocompletar']);

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
            echo $form->field($model, 'IdEstudio')->widget(Select2::className(), [
                'options' => [
                    'placeholder' => 'Buscar estudio...'
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
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <?= Html::submitButton('Guardar', ['class' => 'btn btn-primary',]) ?>  
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>