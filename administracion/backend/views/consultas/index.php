<?php

use common\models\Consultas;
use common\components\PermisosHelper;
use common\models\forms\BusquedaForm;
use common\models\forms\DerivarConsultaForm;
use dosamigos\datepicker\DateRangePicker;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\LinkPager;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $busqueda BusquedaForm */

$this->title = 'Consultas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <div id="errores"></div>
                <?php $form = ActiveForm::begin(['layout' => 'inline',]); ?>

                <?= $form->field($busqueda, 'FechaInicio')->widget(DateRangePicker::className(), [
                        'attributeTo' => 'FechaFin',
                        'labelTo' => 'al',
                        'form' => $form,
                        'language' => 'es',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd/mm/yyyy',
                            'endDate' => 'd',
                        ]
                    ])->label('')
                ?>
                <?php if (count($difusiones) > 0): ?>
                <?= $form->field($busqueda, 'Id')->dropDownList(ArrayHelper::merge(['0' => 'Todas'], ArrayHelper::map($difusiones, 'IdDifusion', 'Difusion'))) ?>
                <?php endif; ?>
                
                <?= $form->field($busqueda, 'Cadena')->textInput(['placeholder' => 'Ingrese búsqueda']) ?>
                        
                <?= $form->field($busqueda, 'Combo')->dropDownList(ArrayHelper::merge(['0' => 'Todas'], Consultas::ESTADOS)) ?> 
                                
                <?= Html::submitButton('Buscar', ['class' => 'btn btn-default', 'name' => 'buscarconsultas-button']) ?> 

                <?php ActiveForm::end(); ?>
                <div class="clearfix"></div>
                <br>
                <?php if (count($models) > 0): ?>
                    <table class="table table-hover">
                        <thead>
                            <tr class="tabla-header">
                                <th>Fecha alta</th>
                                <th>Persona</th>
                                <th>Teléfono</th>
                                <th>Campaña</th>
                                <th>Texto</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($models as $model): ?>
                            <tr>
                                <td><?= Html::encode($model['FechaAlta']) ?></td>
                                <td><?= Html::encode($model['Persona']) ?></td>
                                <td><?= Html::encode($model['Telefono']) ?></td>
                                <td><?= Html::encode($model['Difusion']) ?></td>
                                <td><?= Html::encode($model['Texto']) ?></td>
                                <td><?= ($model['Estado'] == 'D') ?
                                        Html::encode(Consultas::ESTADOS[$model['Estado']].
                                            ' '.$model['Estudio']. ' (' . DerivarConsultaForm::ESTADOS[$model['EstadoDerivacion']].')')
                                    :
                                        Html::encode(Consultas::ESTADOS[$model['Estado']]);
                                    ?>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <?php if ($model['Estado'] == 'A'): ?>
                                            <?php if (PermisosHelper::tienePermiso('DerivarConsulta')): ?>
                                            <button class="btn btn-default"
                                                    data-modal="<?= Url::to(['consultas/derivar',
                                                        'id' => $model['IdConsulta']]) ?>"
                                                        title="Derivar">
                                                <i class="fa fa-external-link" style="color: blue"></i>
                                            </button>
                                            <?php endif; ?>
                                            <?php if (PermisosHelper::tienePermiso('DarBajaConsulta')): ?>
                                            <button class="btn btn-default"
                                                    data-ajax="<?= Url::to(['consultas/dar-baja',
                                                        'id' => $model['IdConsulta']]) ?>"
                                                        title="Dar de baja">
                                                <i class="fa fa-ban" style="color: red"></i>
                                            </button>
                                            <?php endif; ?>
                                        <?php endif;?>
                                        <?php if ($model['Estado'] == 'B'): ?>
                                            <?php if (PermisosHelper::tienePermiso('ActivarConsulta')): ?>
                                            <button class="btn btn-default"
                                                    data-ajax="<?= Url::to(['consultas/activar',
                                                        'id' => $model['IdConsulta']]) ?>"
                                                        title="Modificar">
                                                <i class="fa fa-check-circle" style="color: green"></i>
                                            </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if (PermisosHelper::tienePermiso('ModificarConsulta') && $model['Estado'] != 'D'): ?>
                                        <button class="btn btn-default"
                                                data-modal="<?= Url::to(['consultas/modificar',
                                                    'id' => $model['IdConsulta']]) ?>"
                                                    title="Modificar">
                                            <i class="fa fa-pencil" style="color: dodgerblue"></i>
                                        </button>
                                        <?php endif; ?>
                                        <?php if (PermisosHelper::tienePermiso('BorrarConsulta') && $model['Estado'] != 'D'): ?>
                                        <button class="btn btn-default"
                                            data-ajax="<?= Url::to(['consultas/borrar',
                                                'id' => $model['IdConsulta']]) ?>"
                                            data-hint="Borrar">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                        <?php endif; ?> 
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <strong>No hay campañas de difusión que coincidan con el criterio de búsqueda</strong>
                <?php endif; ?>
            </div>
            <?= LinkPager::widget(['pagination' => $paginacion]) ?>
        </div>
    </div>
</div>