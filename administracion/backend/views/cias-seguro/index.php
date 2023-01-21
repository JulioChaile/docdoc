<?php

use common\components\PermisosHelper;
use backend\models\forms\BusquedaForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $busqueda BusquedaForm */

$this->title = 'Compañias de Seguro';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <div id="errores"></div>
                <button class="btn btn-primary pull-right" 
                        data-modal="<?= Url::to(['/cias-seguro/alta']) ?>">
                    <i class="fa fa-plus"></i> Nueva compañia
                </button>

                <br>
                <?php if (count($models) > 0): ?>
                    <table class="table table-hover">
                        <thead>
                            <tr class="tabla-header">
                                <th>Compañia</th>
                                <th>Telefono</th>
                                <th>Direccion</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($models as $model): ?>
                            <tr>
                                <td><?= Html::encode($model['CiaSeguro']) ?></td>
                                <td><?= Html::encode($model['Telefono']) ?></td>
                                <td><?= Html::encode($model['Direccion']) ?></td>
                                <td>
                                    <div class="btn-group">
                                        <?php if (PermisosHelper::tienePermiso('ModificarDifusion')): ?>
                                        <button class="btn btn-default"
                                                data-modal="<?= Url::to(['cias-seguro/modificar',
                                                    'id' => $model['IdCiaSeguro']]) ?>"
                                                    title="Modificar">
                                            <i class="fa fa-pencil" style="color: dodgerblue"></i>
                                        </button>
                                        <?php endif; ?>
                                        <?php if (PermisosHelper::tienePermiso('BorrarTipoCaso')): ?>
                                            <button class="btn btn-default"
                                                data-ajax="<?= Url::to(['cias-seguro/borrar',
                                                    'id' => $model['IdCiaSeguro']]) ?>">
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
                    <strong>No hay compañias de seguro cargadas</strong>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>