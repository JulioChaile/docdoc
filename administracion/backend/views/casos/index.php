<?php

use common\components\FechaHelper;
use backend\models\forms\BusquedaForm;
use dosamigos\datepicker\DateRangePicker;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $busqueda BusquedaForm */
$tipos = [
    'T' => 'Todos',
    'E' => 'Expediente',
    'C' => 'Caratula',
    'R' => 'Carpeta',
];
$this->title = 'Casos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <div id="errores"></div>
                <?php $form = ActiveForm::begin(['layout' => 'inline',]); ?>
                <div class="col-md-12">
                    <div class="col-md-1">
                         <strong>Estudio: </strong>
                    </div>
                    <div class="col-md-3">
                       
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
                            echo $form->field($busqueda, 'Id')->widget(Select2::className(), [
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
                    <div class="col-md-1">
                        <strong>Abogad@: </strong>
                    </div>
                    <div class="col-md-3">
                        
                        <?php
                            $url = Url::to(['/estudios/autocompletar-usuarios']);

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
                            echo $form->field($busqueda, 'Id2')->widget(Select2::className(), [
                                'options' => [
                                    'placeholder' => 'Buscar usuario...',
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'ajax' => [
                                        'url' => $url,
                                        'dataType' => 'json',
                                        'data' => new JsExpression('function(param){ return { idEstudio: $("#derivarconsultaform-idestudio").val(),'
                                                . ' cadena : param.term }}'),
                                        'processResults' => new JsExpression('function(data,page) { return {results:data}; }')
                                    ],
                                    'initSelection' => new JsExpression($initScript),
                                ]
                            ]);
                            ?>
                    </div>
                    <div class="col-md-1">
                        <strong>Nominación: </strong>
                    </div>
                    <div class="col-md-3">          
                        <?= $form->field($busqueda, 'Combo3')
                            ->dropDownList(
                                ArrayHelper::merge(['0' => 'Todas'], ArrayHelper::map($nominaciones, 'IdNominacion', 'Nominacion'))
                        ) ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-1">
                        <strong>Estados: </strong>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($busqueda, 'Combo4')
                            ->dropDownList(
                                ArrayHelper::merge(['0' => 'Todos'], ArrayHelper::map($subestados, 'IdSubEstadoCaso', 'SubEstadoCaso'))
                            ) ?>
                    </div>
                    <div class="col-md-1">
                        <strong>Fechas: </strong>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($busqueda, 'FechaInicio')->widget(DateRangePicker::className(), [
                            'attributeTo' => 'FechaFin',
                            'labelTo' => 'al',
                            'form' => $form,
                            'language' => 'es',
                            'clientOptions' => [
                                'autoclose' => true,
                                'format' => 'dd/mm/yyyy',
                            ]
                        ])->label('')
                        ?>
                    </div>
                    <div class="col-md-1">
                        <strong>Criterio: </strong>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($busqueda, 'Combo')->dropDownList($tipos) ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-1">
                        <strong>Cadena: </strong>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($busqueda, 'Cadena')->input('text', ['placeholder' => 'Ingrese texto de búsqueda']) ?>
                    </div>
                    <div class="col-md-1">
                        <strong>Tipo: </strong>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($busqueda, 'Combo6')
                            ->dropDownList(
                                ArrayHelper::merge(['0' => 'Todos'], ArrayHelper::map($tiposCaso, 'IdTipoCaso', 'TipoCaso'))
                            ) ?>
                    </div>
                </div>
                                
                <?= Html::submitButton('Buscar', ['class' => 'btn btn-default pull-right', 'name' => 'buscarconsultas-button']) ?> 

                <?php ActiveForm::end(); ?>
                <div class="clearfix"></div>
                <br>
                <?php if (count($models) > 0): ?>
                    <table class="table table-hover table-condensed">
                        <thead>
                            <tr class="tabla-header">
                                <th>Fecha alta</th>
                                <th>Estudio</th>
                                <th>Juzgado</th>
                                <th>Tipo</th>
                                <th>Caratula</th>
                                <th>Estado</th>
                                <th>Últ. visita</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($models as $model): ?>
                            <tr>
                                <td><?= Html::encode(FechaHelper::formatearDateLocal($model['FechaAlta'])) ?></td>
                                <td><?= Html::encode($model['Estudio']) ?></td>
                                <td><?= Html::encode($model['Juzgado']) ?></td>
                                <td><?= Html::encode($model['TipoCaso']) ?></td>
                                <td><?= Html::encode($model['Caratula']) ?></td>
                                <td><?= Html::encode($model['EstadoCaso']) ?></td>
                                <td><?= Html::encode(FechaHelper::formatearDatetimeLocal($model['FechaUltVisita'])) ?></td>
                                <td>
                                    <div class="btn-group-sm">
                                        <button class="btn btn-default"
                                                data-modal="<?= Url::to(['casos/listar-personas',
                                                    'id' => $model['IdCaso']]) ?>"
                                                title="Personas"
                                                >
                                            <i class="fa fa-users"></i>
                                        </button>
                                        <button class="btn btn-default"
                                                data-modal="<?= Url::to(['casos/listar-movimientos',
                                                    'id' => $model['IdCaso']]) ?>"
                                                title="Movimientos"
                                                >
                                            <i class="fa fa-bars"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <strong>No hay casos que coincidan con el criterio de búsqueda</strong>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>