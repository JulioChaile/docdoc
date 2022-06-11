<?php

use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

$this->title = 'Estudio '.$estudio['Estudio'];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="nav-tabs-custom">
            <?= $this->render('tabs', ['IdEstudio' => $estudio['IdEstudio']])?>
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">                     
                    <div class="row">
                        <?php $form = ActiveForm::begin() ?>
                        
                        <div class="col-md-6">
                            <?= $form->field($estudio, 'Estudio') ?>
                            
                            <?= $form->field($estudio, 'Domicilio') ?>
                            
                            <?= $form->field($estudio, 'Telefono') ?>
                        </div>
                        <div class="col-md-6">
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
                            echo $form->field($estudio, 'IdProvincia')->widget(Select2::className(), [
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
                            echo $form->field($estudio, 'IdCiudad')->widget(Select2::className(), [
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
                            
                            <?= Html::submitButton('Guardar', ['class' => 'btn btn-primary pull-right']) ?>
                        </div>
                        <?php ActiveForm::end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
