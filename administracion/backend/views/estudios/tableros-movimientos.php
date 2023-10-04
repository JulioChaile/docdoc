<?php

use common\components\PermisosHelper;
use common\models\forms\BusquedaForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $busqueda BusquedaForm */

$this->title = 'Tableros de Movimientos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        
        <div class="nav-tabs-custom">
            <?= $this->render('tabs', ['IdEstudio' => $estudio['IdEstudio']])?>
            <div class="tab-content" >
                <div class="tab-pane active" id="tab_1">
                    <?php
                    foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
                        echo '<div class="alert alert-' . $key . ' alert-dismissable">'
                        . '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'
                        . $message . '</div>';
                    }
                    ?>
                    <div class="box-body">
                        <div id="errores"></div>
                        <?php if (PermisosHelper::tienePermiso('AltaOrigen')): ?>
                            <button class="btn btn-primary pull-right" 
                                    data-modal="<?= Url::to(['/estudios/alta-tablero-movimiento',
                                        'id' => $estudio['IdEstudio']]) ?>">
                                <i class="fa fa-plus"></i> Nuevo Tablero
                            </button>
                        <?php endif; ?> 
                        <br>
                        <?php if (count($models) > 0): ?>
                            <table class="table table-hover">
                                <thead>
                                    <tr class="tabla-header">
                                        <th>Tipo de Movimiento</th>
                                        <th>Orden</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($models as $model): ?>
                                    <tr>
                                        <td><?= Html::encode($model['TipoMovimiento']) ?></td>                               
                                        <td><?= Html::encode($model['Orden']) ?></td>                               
                                        <td>
                                            <div class="btn-group">
                                                <?php if (PermisosHelper::tienePermiso('ModificarOrigen')): ?>
                                                <button class="btn btn-default"
                                                        data-modal="<?= Url::to(['estudios/modificar-orden-tablero',
                                                            'id' => $model['IdTipoMovimientoTablero']]) ?>"
                                                            title="Modificar">
                                                    <i class="fa fa-pencil" style="color: dodgerblue"></i>
                                                </button>
                                                <?php endif; ?>
                                                <?php if (PermisosHelper::tienePermiso('BorrarOrigen')): ?>
                                                    <button class="btn btn-default"
                                                        data-ajax="<?= Url::to(['estudios/borrar-tipo-movimiento-tablero',
                                                            'id' => $model['IdTipoMovimientoTablero']]) ?>">
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
                            <strong>Aún no se cargaron tipos de movimiento al tablero.</strong>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
