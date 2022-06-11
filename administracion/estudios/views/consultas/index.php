<?php

use common\components\FechaHelper;
use common\models\forms\DerivarConsultaForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Consultas Derivadas';
?>
<div class="row">
    <div class="box">
        <div class="box-body">
            <?php $form = ActiveForm::begin(['layout' => 'inline']) ?>
            
            <?= $form->field($busqueda, 'Cadena') ?>
            
            <?= $form->field($busqueda, 'Combo')->dropDownList(ArrayHelper::merge(['T' => 'Todos'], DerivarConsultaForm::ESTADOS)) ?>
            
            <?= Html::submitButton('Buscar', ['class' => 'btn btn-default']) ?>
            
            <?php ActiveForm::end() ?>
            <div id="errores"></div>
            <div class="row" style="margin-top: 2em">
                <div class="col-md-12">
                    <?php if (count($consultas) > 0): ?>
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <td>Fecha Derivación</td>
                                <td>Persona</td>
                                <td>Consulta</td>
                                <td>Telefono</td>
                                <td>Fecha Consulta</td>
                                <td>Estado</td>
                                <td>Acciones</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($consultas as $consulta): ?>
                            <tr>
                                <td><?= Html::encode(FechaHelper::formatearDatetimeLocal($consulta['FechaDerivacion'])) ?></td>
                                <td><?= Html::encode($consulta['Apynom']) ?></td>
                                <td><?= Html::encode($consulta['Texto']) ?></td>
                                <td><?= Html::encode($consulta['Telefono']) ?></td>
                                <td><?= Html::encode(FechaHelper::formatearDateLocal($consulta['FechaAlta'])) ?></td>
                                <td><?= Html::encode(DerivarConsultaForm::ESTADOS[$consulta['EstadoDerivacion']]) ?></td>
                                <td>
                                    <div class="btn-group-sm">
                                        <?php if ($consulta['EstadoDerivacion'] == 'P'): ?>
                                        <button class="btn btn-default"
                                                data-ajax="<?= Url::to(['consultas/aceptar',
                                                    'id' => $consulta['IdDerivacionConsulta']]) ?>">
                                            <i class="fa fa-check-circle" style="color: green"></i>
                                        </button>
                                        <button class="btn btn-default"
                                                data-ajax="<?= Url::to(['consultas/rechazar',
                                                    'id' => $consulta['IdDerivacionConsulta']]) ?>">
                                            <i class="fa fa-minus-circle" style="color: red"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <p><strong>No se registran consultas con los criterios de búsqueda indicados.</strong></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>