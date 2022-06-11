<?php

use common\models\EstadosCaso;
use common\components\PermisosHelper;
use common\models\forms\BusquedaForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $busqueda BusquedaForm */

$this->title = $estudio['Estudio'].' - Estados de Caso';
$this->params['breadcrumbs'] = [
    [
        'label' => 'Estudios',
        'url' => ['/estudios']
    ],
    $this->title
];
?>
<div class="row">
    <div class="col-md-12">
        <div class="nav-tabs-custom">
            <?= $this->render('tabs', ['IdEstudio' => $estudio['IdEstudio']])?>
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">                     
                    <div class="box-body">
                        <div id="errores"></div>
                        <?php if (PermisosHelper::tienePermiso('AltaEstadoCaso')): ?>
                            <button class="btn btn-primary pull-right" 
                                    data-modal="<?= Url::to(['/estudios/alta-estado-caso', 'id' => $estudio['IdEstudio']]) ?>">
                                <i class="fa fa-plus"></i> Nuevo estado
                            </button>
                        <?php endif; ?>

                        <?php if (count($estadosCaso) > 0): ?>
                        <table class="table table-hover">
                            <thead>
                                <tr class="tabla-header">
                                    <th>Nombre</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($estadosCaso as $model): ?>
                                <tr>
                                    <td><?= Html::encode($model['EstadoCaso']) ?></td>
                                    <td><?= Html::encode(EstadosCaso::ESTADOS[$model['Estado']]) ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <?php if ($model['Estado'] == 'A'): ?>
                                                <?php if (PermisosHelper::tienePermiso('DarBajaEstadoCaso')): ?>
                                                <button class="btn btn-default"
                                                    data-ajax="<?= Url::to(['estudios/dar-baja-estado-caso',
                                                        'id' => $model['IdEstadoCaso']]) ?>"
                                                        title="Dar de Baja">
                                                    <i class="fa fa-minus-circle" style="color: red"></i>
                                                </button>
                                                <?php endif;?>
                                            <?php else: ?>
                                                <?php if (PermisosHelper::tienePermiso('ActivarEstadoCaso')): ?>
                                                <button class="btn btn-default"
                                                    data-ajax="<?= Url::to(['estudios/activar-estado-caso',
                                                        'id' => $model['IdEstadoCaso']]) ?>"
                                                        title="Dar de Baja">
                                                    <i class="fa fa-check-circle" style="color: green"></i>
                                                </button>
                                                <?php endif;?>
                                            <?php endif; ?>
                                            <?php if (PermisosHelper::tienePermiso('ModificarEstadoCaso')): ?>
                                            <button class="btn btn-default"
                                                    data-modal="<?= Url::to(['estudios/modificar-estado-caso',
                                                        'id' => $model['IdEstadoCaso']]) ?>"
                                                        title="Modificar">
                                                <i class="fa fa-pencil" style="color: dodgerblue"></i>
                                            </button>
                                            <?php endif; ?>
                                            <?php if (PermisosHelper::tienePermiso('BorrarEstadoCaso')): ?>
                                                <button class="btn btn-default"
                                                    data-ajax="<?= Url::to(['estudios/borrar-estado-caso',
                                                        'id' => $model['IdEstadoCaso']]) ?>">
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
                        <strong>No hay estados de casos que coincidan con el criterio de búsqueda</strong>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>