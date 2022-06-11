<?php

use common\components\FechaHelper;
use common\models\forms\BusquedaForm;
use common\models\Casos;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Casos del estudio';
?>
<div class="row">
    <div class="box">
        <div class="box-body">
            <?php $form = ActiveForm::begin(['layout' => 'inline']) ?>
            
            <?= $form->field($busqueda, 'Cadena') ?>
            
            <?= $form->field($busqueda, 'Combo')->dropDownList(ArrayHelper::merge(['T' => 'Todos'], Casos::ESTADOS)) ?>
            
            <?= Html::submitButton('Buscar', ['class' => 'btn btn-default']) ?>
            
            <?php ActiveForm::end() ?>
            <div id="errores"></div>
            <div class="row" style="margin-top: 2em">
                <div class="col-md-12">
                    <?php if (count($casos) > 0): ?>
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <td>Caratula</td>
                                <td>Expediente</td>
                                <td>Fecha alta</td>
                                <td>Nominacion</td>
                                <td>Juzgado</td>
                                <td>Estado de Caso</td>
                                <td>Tipo de Caso</td>
                                <td>Fecha del último movimiento</td>
                                <td>Acciones</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($casos as $caso): ?>
                            <tr>
                                <td><?= Html::encode($caso['Caratula']) ?></td>
                                <td><?= Html::encode($caso['NroExpediente']) ?></td>
                                <td><?= Html::encode($caso['FechaAlta']) ?></td>
                                <td><?= Html::encode($caso['Nominacion']) ?></td>
                                <td><?= Html::encode($caso['Juzgado']) ?></td>
                                <td><?= Html::encode($caso['EstadoCaso']) ?></td>
                                <td><?= Html::encode($caso['TipoCaso']) ?></td>
                                <td><?= Html::encode($caso['FechaUltimoMov']) ?></td>
                                <td>
                                    <div class="btn-group-sm">
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <p><strong>No se registran casos con los criterios de búsqueda indicados.</strong></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>